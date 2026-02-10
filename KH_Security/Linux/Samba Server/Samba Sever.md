# Samba Server

## Samba 개요
- 네트워크를 통해 파티션을 공유하도록 제공하는 서비스이다.
- 유닉스 시스템과 Windows 시스템 간 파일 시스템 공유가 가능하다.
- 유닉스 계열의 거의 모든 시스템에서 제공된다.
- 응용을 제공하는 것이 아니라 시스템의 리소스를 직접 제공하는 서비스이므로 보안에 주의한다.

---

## Samba 서버 설치 후 확인
```
  dnf install -y samba
```

![01](/KH_Security/Linux/Samba%20Server/img/01.png)

---

## 데몬 및 관련 파일
- 데몬 : `/usr/sbin/smbd`
  - 공유 기능을 담당하는 데몬이며,
  - 139번 포트 이용합니다.
- 데몬 실행 스크립트 : `/usr/lib/systemd/system/smb.service`
  - systemd에서 Samba 데몬(smbd)을 제어하기 위한 서비스 파일입니다.
- 환경 설정 파일 : `/etc/samba/smb.conf`
  - Samba 서버의 동작을 정의하는 환경 설정 파일입니다.

---

## 데몬 실행
```
  systemctl start smb.service
```

---

## Samba 관련 명령어

- `smbpasswd`  
  Samba 사용자 등록 및 비밀번호를 관리합니다.  
  -a : 사용자 추가  
  -x : 사용자 삭제  
  -d / -e : 사용자 비활성화 / 활성화  

- `pdbedit -wL`  
  Samba 사용자 데이터베이스에 등록된 계정 목록을 확인합니다.

- `testparm`
  smb.conf 파일의 문법 오류를 검사합니다.

- `nmblookup`  
  NetBIOS 이름 해석을 수행합니다.

---

## Samba 사용자 등록

- Samba 사용자는 Linux 계정과 별도로 관리됩니다.
- 다음 명령어로 Samba 계정을 등록합니다.
```
  smbpasswd -a ast16
```
![02](/KH_Security/Linux/Samba%20Server/img/02.png)

---

## 공유 디렉터리 생성 및 권한 설정

- 공유에 사용할 디렉터리를 생성합니다.
```
  mkdir -p /home/pub /home/public /home/pro1 /home/pro2 /home/project
```

- 파일 생성, 수정, 삭제 실습을 위해 퍼미션을 777로 설정합니다.
```
  chmod -R 777 /home/pub /home/public /home/pro1 /home/pro2 /home/project
```

![03](/KH_Security/Linux/Samba%20Server/img/03.png)

---

## smb.conf 설정

### smb.conf [Global] 설정
- `workgroup` : Windows 네트워크에서 사용할 워크그룹 또는 NT 도메인 이름을 지정합니다.

- `netbios name` : 네트워크 상에서 Samba 서버를 식별할 이름을 지정합니다.  
지정하지 않으면 호스트명이 기본값으로 사용됩니다.

- `hosts allow`, `hosts deny` : Samba 서버에 접근 가능한 클라이언트를 허용/제한 합니다.

- `guest account` : Windows의 guest 사용자를 Linux 계정으로 매핑합니다.  
일반적으로 nobody 계정을 사용하며, 주석 처리 시 guest 접속은 차단됩니다.

- `security = user`  
  사용자 인증 기반 접근 제어를 사용합니다.

- `passdb backend = tdbsam`  
  Samba 사용자 정보를 tdb 데이터베이스에 저장합니다.

---

### Symlink 디렉토리 접근 설정
- `follow symlinks = yes`
- `wide links = yes`
- `unix extensions = no`

---

### [homes] 공유 설명

- [homes]는 Samba에서 기본 제공하는 공유입니다.
- 각 사용자의 홈 디렉터리를 자동으로 공유합니다.
- 사용자는 자신의 홈 디렉터리에만 접근할 수 있습니다.

---

### smb.conf 파일 설정

- 다음과 같이 설정을 해주면 됩니다.  
![04](/KH_Security/Linux/Samba%20Server/img/04.png)

#### smb.conf [Share] 주요 옵션 설명

- `path = /home/data`  
  실제 공유할 디렉터리 경로를 지정합니다.

- `browseable = yes / no`  
  네트워크 탐색기에 공유 목록을 표시할지 여부를 지정합니다.

- `writable = yes`  
  공유 디렉터리에 대한 쓰기 접근을 허용합니다.

- `read only = yes`  
  기본적으로 읽기 전용으로 접근을 제한합니다.

- `public = yes / no`  
  인증 없이 접근을 허용할지 여부를 지정합니다.

- `valid users = ast10`  
  접근 가능한 사용자를 제한합니다.

- `write list = +test ast10`  
  쓰기 권한을 허용할 사용자 또는 그룹을 지정합니다.
  
- `inherit acls = yes`  
  상위 디렉터리의 ACL 권한을 하위 파일과 디렉터리에 상속합니다.

---

## Samba Server 실행

```text
  systemctl start smb
```

---

## Windows 환경에서 공유 디렉터리 확인

- 접속 시 명령어
```
  \\192.168.10.127
```

- `Public = no`로 설정 시 삼바 계정 등록 시에
정했던 비밀번호 입력 후 접근합니다.

![05](/KH_Security/Linux/Samba%20Server/img/05.png)

---

## Linux 환경에서 파일 생성 후 Windows 환경에서 파일 확인

```text
  echo test ast06 > /home/pub/test.txt
  ls ll
```

- **Linux에서 파일 생성**  
![06](/KH_Security/Linux/Samba%20Server/img/06.png)

- **Windows에서 파일 확인**  
![07](/KH_Security/Linux/Samba%20Server/img/07.png)

---

## Windows 환경에서 파일 생성 후 Linux 환경에서 파일 확인

- **Windows에서 파일 생성**  
![08](/KH_Security/Linux/Samba%20Server/img/08.png)

- **Windows에서 파일 확인**  
![09](/KH_Security/Linux/Samba%20Server/img/09.png)
