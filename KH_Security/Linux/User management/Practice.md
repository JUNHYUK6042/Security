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

## 그룹 관리 (생성 및 삭제)

#### 그룹 생성 (group add)

```text
groupadd -g 그룹번호 그룹명
```

![02](/KH_Security/Linux/User%20management/img/02.png)

- **주요 명령어 실행 예시:**
    - `groupadd -g 3000 st`: GID를 3000으로 지정하여 생성
    - 생성 후 그룹이 생성 되었는지 확인합니다.

---

## 그룹 삭제 (groupdel)

- 그룹삭제는 `groupdel [그룹명]`으로 삭제할 수 있습니다.
- `groupdel group4`로 group4를 제거한 것을 확인할 수 있습니다.

![04](/KH_Security/Linux/User%20management/img/04.png)

---

## 사용자 생성 (useradd)

- 사용자를 생성할 때 특정 그룹(`-g`)이나 UID(`-u`)를 지정할 수 있습니다. 형태는 다음과 같습니다.
```text
useradd [옵션] 사용자명
```

![03](/KH_Security/Linux/User%20management/img/02.png)

- **주요 옵션 설명:**
    - `-g`: 사용자의 주 그룹(Primary Group)을 지정
    - `-u`: 사용자 계정의 UID(User ID)를 직접 지정
    - `-G`: 사용자의 보조 그룹 지정 (사용자는 여러 그룹에 속할 수 있음)
    - `-s`: 기본 쉘 지정 (보안용 계정은 `/sbin/nologin`으로 설정)

---

## 사용자 삭제 (userdel)

```text
명령어 : userdel [옵션] [계정명]
사용자 삭제는 userdel [옵션] 사용자명로 할 수 있습니다.
사용자 계정을 삭제할 때는 반드시 -r 옵션을 포함하여 홈 디렉터리와 메일함까지 함께 삭제해야 합니다.
```

![05](/KH_Security/Linux/User%20management/img/05.png)

- st02 계정을 삭제하고 나서 확인 했을때 없다는 것을 확인할 수 있습니다.

---

## 사용자 비밀번호 변경

- 명령어
```text
passwd [사용자 계정]를 통해 비밀번호를 생성 및 변경합니다.
```

![06](/KH_Security/Linux/User%20management/img/06.png)

- 8글자 수 보다 적게 설정 시 8개의 문자보다 짧다는 알림이 뜹니다.
- root 계정으로 접근 하지 않을 시 `sudo su` 명령어를 통해 root권한에 접근하여 변경해줍니다.

---
