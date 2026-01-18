# SSH Key

## 개요

- PuTTYgen은 PuTTY로 SSH 원격 접속을 할 때 공개키와 개인키를 생성하여 비밀번호 없이 인증서 방식으로 로그인할 수 있도록 해주는 도구입니다.
- 비밀번호 인증 방식보다 보안성이 높으며 무차별 대입 공격에 매우 강합니다.
- 공개키는 서버에 저장되며 유출되어도 직접적인 위험은 없습니다.
- 개인키는 클라이언트에 저장되며 유출 시 즉시 폐기 및 재발급이 필요합니다.
- 본 문서는 PuTTYgen을 이용한 키 생성부터 서버 설정, PuTTY 접속까지의 전체 과정을 정리합니다.

---

## PuTTYgen 실행 및 키 생성

- PuTTY Key Generator(PuTTYgen)를 실행하면 다음과 같은 키 생성 화면이 나타납니다.
- Generate 버튼을 눌러 키를 생성 합니다.

![01](/KH_Security/VMware/Putty%20Gen/img/01.png)

- 키 생성 후 Save private key를 클릭 시 개인 키가 생성됩니다.
- 개인키는 따로 저장 하는 곳에 저장해두면 좋습니다.

![02](/KH_Security/VMware/Putty%20Gen/img/02.png)

---

## 서버 공개키 저장

```text
1. .ssh 디렉터리 생성
2. .ssh 디렉터리 밑에 authorized_keys에 공개키 저장
3. 권한 설정
```

### .ssh 디렉터리 생성 및 파일 생성

```text
mkdir .ssh
vi .ssh/authoriezd_keys
```

![03](/KH_Security/VMware/Putty%20Gen/img/03.png)

### .ssh 디렉터리 밑에 authorized_keys에 공개키 저장

- 다음과 같이 파일 안에 복사한 공개키를 파일안에 입력 후  
`:wq` 명령어를 입력후 저장합니다.

![04](/KH_Security/VMware/Putty%20Gen/img/04.png)

- 파일 내용 확인

![05](/KH_Security/VMware/Putty%20Gen/img/05.png)

### 권한 설정

- 명령어
```text
chmod -R 700 .ssh
```

![06](/KH_Security/VMware/Putty%20Gen/img/06.png)

---

## PuTTY 설정

```text
Connection -> SSH -> Auth -> Credentials로 이동합니다.
Private key file for authentication 항목에서 저장한 개인키 파일을 선택합니다.
```

![07](/KH_Security/VMware/Putty%20Gen/img/07.png)

```text
PuTTY Configuration -> Connection -> Data 이동합니다.
Auto -> login username 항목에 로그인 계정을 입력합니다.
본 실습에서는 기존에 만들어둔 root 계정을 사용합니다.
```

![08](/KH_Security/VMware/Putty%20Gen/img/08.png)

## 접속

- 설정 완료 후 PuTTY로 서버에 접속합니다.
- 비밀번호 입력 없이 공개키와 개인키 기반으로 로그인이 성공합니다.

![09](/KH_Security/VMware/Putty%20Gen/img/09.png)

---
