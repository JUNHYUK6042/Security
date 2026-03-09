# John the Ripper

## 개요
- John the Ripper는 패스워드 해시(hash)를 크랙하기 위한 대표적인 패스워드 크래킹 도구입니다.
- Linux 시스템의 `/etc/passwd`, `/etc/shadow` 파일을 이용하여 사용자 패스워드를 크랙할 수 있습니다.

### 특징
- 패스워드 해시 크랙 도구
- Dictionary 공격 지원
- Brute Force 공격 지원
- 다양한 해시 알고리즘 지원

---

## John the Ripper 설치
- 명령어
```text
apt install -y john
```

![01](/KH_Security/모의%20해킹/John%20the%20Ripper/img/01.png)
- 위의 결과처럼 설치 후 `apt show john` 명령어를 통해 설치가 되었는지 확인해줍니다.

---

## John the Ripper 설정

- John the Ripper 배포본은 아직YESCRYPT 방식의 hash를 지원하지 않기 때문에 다음과 같이 수정합니다.
-  YESCRYPT -> SHA512로 수정합니다.


- `/etc/login.defs :` 
  - 리눅스 계정 생성 정책을 설정하는 파일입니다.  
  즉 사용자 계정 생성 시 적용되는 기본 설정을 정의합니다.
```text
vi /etc/login.defs
```
![02](/KH_Security/모의%20해킹/John%20the%20Ripper/img/02.png)

- `/etc/pam.d/common-password :` 
  - PAM(Pluggable Authentication Module) 인증 설정 파일입니다.  
  즉, 리눅스에서 패스워드를 어떻게 처리할지 실제로 결정하는 파일입니다.
```text
vi /etc/pam.d/common-password
```
![03](/KH_Security/모의%20해킹/John%20the%20Ripper/img/03.png)

---

## Password 크랙

### STEP 1.암호 파일 생성

- unshadow 명령어
  - `문법 : unshadow [passwd 파일] [shadow 파일]`
```text
unshadow /etc/passwd /etc/shadow > passwd.1
```

- unshadow 명령어를 통해 암호 파일을 생성합니다.

### STEP 2.암호 크랙

- john 명령어
  - `문법 : jhon [암호 파일]
```text
john passwd.1
```

- 다음과 같이 passwd.1 암호 파일을 통해 암호 크랙을 시도합니다.

![04](/KH_Security/모의%20해킹/John%20the%20Ripper/img/04.png)

- 크랙 암호 확인
```
cat ~/.jhon/john.pot
```

- 진행 확인
```text
john --show passwd.1
john --status
```
