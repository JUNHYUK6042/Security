# User Management & Group Management

- 사용자(User)와 그룹(Group)을 적절히 생성하고 권한 및 암호 정책을 관리하는 과정

---

## /etc/passwd 사용자 등록 정보

- 사용자의 기본 등록 정보가 저장되고, useradd 명령을 이용하여 등록합니다.
- cat /etc/passwd 명령어를 통해 시스템에 등록된 정보를 확인할 수 있습니다.

![01](/KH_Security/Linux/User%20management/img/01.png)

- root 계정 정보 : `root:x:0:0:root:/root:/bin/bash`
- 앞에서부터 순서대로 `계정 : 암호 : UID : GID : 주석 :홈디렉터리 : 쉘 이렇게 구성되어 있다.`

---

## /etc/shadow 사용자 패스워드, 암호정책 정보

- 사용자의 암호 해시값과 상세 암호 정책(만료일 등)은 /etc/shadow 파일에서 관리됩니다.
- `계정명 : 암호 : 최종변경일 : 암호최소사용일 : 암호최대유효기간 : 경고기간 : 계정폐쇄기간 : 계정만료일`
- $6 : 해시 알고리즘 (`SHA512` 사용)

---

## 그룹 관리(생성 및 삭제)

#### 그룹 생성
