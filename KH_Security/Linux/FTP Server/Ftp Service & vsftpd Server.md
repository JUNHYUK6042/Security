# FTP & vsftpd 서버 구성 정리

## 1. FTP 서비스 개요

### FTP(File Transfer Protocol) 특징
- 대용량 파일 전송에 적합한 서비스
- 최근에는 웹 서비스의 일부로 통합 운영되는 추세
- `xinetd`보다 **standalone 방식**으로 운영
- 시스템 리소스를 비교적 많이 사용하는 서비스
- **Out of Band 방식** 사용

### 포트 구성
- **21/TCP** : Control Connection
- **20/TCP** : Data Connection (Active Mode)
- **Passive Mode** : 1024번 이후 임의 포트 사용

---

## 2. Control Connection / Data Connection

### Control Connection (제어 연결)
- FTP 세션 전체를 **관리하는 연결**
- 클라이언트와 서버 사이에 **항상 유지되는 연결**
- **21번 포트** 사용

#### Control Connection에서 처리되는 작업
- 사용자 인증 (ID / Password)
- 명령 전달
- `ls`, `cd`, `pwd`, `get`, `put` 등
- 파일 전송 요청 및 상태 관리

**실제 파일 데이터는 절대 이 연결로 전송되지 않는다**

---

### Data Connection (데이터 연결)
- **파일 전송 또는 디렉토리 목록 전송 전용 연결**
- 파일 하나당 **1개의 연결 생성**
- 전송이 끝나면 즉시 종료됨

#### Data Connection 특징
- Control Connection과 **완전히 분리된 연결**
- 파일 전송, `ls` 결과 출력 등에 사용
- 전송이 끝날 때마다 연결이 닫힘

---

### Control / Data Connection 구조 요약

| 구분 | Control Connection | Data Connection |
|---|---|---|
| 목적 | 명령 제어 | 파일/데이터 전송 |
| 포트 | 21 | 20 또는 임의 포트 |
| 연결 유지 | 세션 동안 유지 | 전송 시에만 생성 |
| 개수 | 1개 | 파일당 1개 |

---

## 3. FTP 접속 및 파일 전송 과정

1. FTP Client가 서버의 **21번 포트**로 Control Connection 생성
2. 사용자 계정과 비밀번호 전송
3. 디렉토리 이동, 파일 목록 조회 명령 전달
4. 파일 전송 요청 시 **Data Connection 생성
5. 파일 전송 완료 후 Data Connection 종료
6. 다음 파일 전송 시 새로운 Data Connection 생성

---

## FTP

### vsftp

- `dnf install -y vsftpd`를 통해 설치해줍니다.
- 다음과 같이 설치가 되어있는지 `systemctl status vsftpd.service` 명령어를 통해 상태 확인을 해줍니다.

![01](/KH_Security/Linux/FTP%20Server/img/01.png)

- `active (running)`가 되어 있으므로 활성화가 되었습니다.

---

### chroot 설정

#### chroot 관련 주요 설정 옵션

```conf
chroot_local_user=YES
chroot_list_enable=YES
chroot_list_file=/etc/vsftpd/chroot_list
allow_writeable_chroot=YES
```

- `chroot`를 설정하기 위해서 사용자 계정을 만들어 준뒤  
`vsftpd.conf` 파일 안에서 수정을 합니다.

### chroot_local_user / chroot_list_enable 동작 방식

```
chroot_list_enable = YES (기본값)
```

- `chroot_local_user=NO` (기본값)
  - 기본적으로 모든 로컬 사용자에게 chroot 미적용
  - `chroot_list_file`에 등록된 사용자만 chroot 적용
  
- `chroot_local_user=YES` (실무에서 가장 많이 사용)
  - 기본적으로 모든 로컬 사용자에게 chroot 적용
  - `chroot_list_file`에 등록된 사용자만 chroot 미적용 (예외 처리)

---

### 심볼릭 링크 접근 제한 및 해결 방법

- chroot 환경에서는 외부 디렉토리를 가리키는 심볼릭 링크 접근 불가
- 해결 방법으로 `mount --bind` 사용

```bash
mount --bind [원본 디렉토리] [연결할 디렉토리]
```

#### 예시
```bash
mount --bind /data/share /home/test01/share
```

---

#### 사용자 계정 생성

- useradd 명령어를 통해 test01, test02, test03 계정을 생성합니다.

---

## chroot 설정 후 실습

- `/etc/vsftpd/vsftpd.conf` 파일에  
다음과 같은 내용을 입력 후 저장해줍니다.

![02](/KH_Security/Linux/FTP%20Server/img/02.png)

- 설정 파일 변경 후 시스템 재부팅은 필요 없으며, 서비스 재시작만으로 적용됩니다.
  - `systemctl restart vsftpd.service`명령어를 통해 재부팅 합니다.


### chroot 적용

- `/etc/vsftpd/chroot_list` 파일에 계정을 적습니다.

![03](/KH_Security/Linux/FTP%20Server/img/03.png)

- chroot_list_enable = YES, chroot_local_user=NO
  - 이런 경우에는 파일안에 있는 계정들은 chroot가 적용
  - 파일 밖에 있는 계정은 chroot가 적용되지 않음

- chroot_list_enable = YES, chroot_local_user=YES
  - 이런 경우에는 파일안에 있는 계정들은 chroot가 적용안됨

- 실습

- `chroot_list_enable = YES, chroot_local_user=YES`
  - 다음과 같이 test1 계정은 chroot가 적용되지 않았습니다.

![04](/KH_Security/Linux/FTP%20Server/img/04.png)    
![05](/KH_Security/Linux/FTP%20Server/img/05.png) 

---

## 사용자 제한 설정 (user_list)

### user_list란?
`user_list`는 **FTP 접속이 허용되거나 차단될 사용자 계정을 정의하는 파일**로,
`vsftpd.conf`의 `userlist_enable`과 `userlist_deny` 옵션에 따라 동작 방식이 결정된다.

- 사용자 목록 파일 경로

```text
/etc/vsftpd/user_list
```

- `userlist_enable`의 기본값은 `NO`
단, **YUM으로 vsftpd를 설치한 경우 기본값이 `YES`로 설정되어 있는 경우가 많다.**

---

### userlist_enable / userlist_deny 동작 방식

| userlist_enable | userlist_deny | 의미 |
|---|---|---|
| YES | YES | `user_list` 파일에 등록된 사용자는 **FTP 접속 불가** |
| YES | NO | `user_list` 파일에 등록된 사용자만 **FTP 접속 가능** |
| NO | 무관 | `user_list` 기능 비활성화 (파일 사용 안 함) |

- 특정 사용자 접속만 허용하고 싶을 때
```conf
userlist_enable=YES
userlist_deny=NO
```


- 특정 사용자 접속을 차단하고 싶을 때
```conf
userlist_enable=YES
userlist_deny=YES
```

### 적용 실습

- 다음과 같이 특정 사용자 접속만 허용하게 설정했습니다.

![06](/KH_Security/Linux/FTP%20Server/img/06.png)

- `/etc/vsftpd/user_list`를 통해 다음과 같이 test01를 입력 후 저장합니다.

![07](/KH_Security/Linux/FTP%20Server/img/07.png)

- 이후 test01로 접속 시에만 FTP접속이 가능한 것을 볼 수 있습니다.

![08](/KH_Security/Linux/FTP%20Server/img/08.png)

--- 

## 사용자 제한 설정 (ftpusers / PAM)

### ftpusers란?

- `ftpusers`는 **PAM(Pluggable Authentication Module)을 이용한 FTP 접속 제한 방식**으로,
- vsftpd 자체 설정(`user_list`)과는 **별도로 동작하는 인증 단계의 접근 제어 기능**이다.

- 제한 설정 파일  
```text
/etc/vsftpd/ftpusers
```

- PAM 설정 파일
```text
/etc/pam.d/vsftpd
```

### sense 옵션에 따른 동작 방식

| sense 값 | 의미 |
|---|---|
| deny | `ftpusers`에 등록된 계정은 FTP 접속 불가 |
| allow | `ftpusers`에 등록된 계정만 FTP 접속 허용 |

---

### /etc/pam.d/vsftpd 설정

- `sense=deny (기본값)`

```conf
auth required pam_listfile.so item=user sense=allow file=/etc/vsftpd/ftpusers onerr=succeed
```

![09](/KH_Security/Linux/FTP%20Server/img/09.png)

- sense값을 `allow`로 바꿉니다.

---

### /etc/pam.d/vsftpd 설정

- test02를 넣어줍니다.

![10](/KH_Security/Linux/FTP%20Server/img/10.png)

---

### 사용자 계정 접속 테스트

- `test01, test02, test03` 사용자 계정이 접속 되는 지 확인해줍니다.

![11](/KH_Security/Linux/FTP%20Server/img/11.png)

- sense = allow인 상태에서 `test02` 계정만 등록되어 있으므로  
접속이 가능하고 나머지 계정은 실패합니다.

---
