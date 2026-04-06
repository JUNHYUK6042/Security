# Rocky(Oracle Linux) 8 환경 Oracle 설치

## 개요

- Rocky(Linux) 8 환경에서 Oracle 19c 설치 과정 정리하는 문서입니다.
- 설치 흐름 : `환경 설정 → 패키지 설치 → 계정 설정 → 설치 실행 → 설치 확인`

---

## Linux 환경 설정

- 여기서 사용되는 리눅스 설치 계정과 그룹은 오라클 관리 계정 및 그룹으로 사용되며,  
편의상 홈 디렉토리는 ORACLE_BASE 디렉토리로 사용됨으로 계정의 이름, 디렉토리 등을 설정할 때 주의해야 합니다.  

- **환경설정 구성도**
```
호스트명, IP : ora19c, 192.168.10.118
설치 계정 : ora19c (UID : 1900)
소속 그룹 : dba (GID : 1900)
홈 디렉토리 : /home/ora19c
$ORACLE_BASE : /app/ora19c
$ORACLE_HOME : /app/ora19c/19c
```

### oracle 관리 계정 및 그룹 생성

```
# groupadd -g 1900 dba
# useradd -g dba -u 1900 ora19c
# passwd ora19c
```

![01](/KH_Security/Linux/Oracle/img/01.png)

- **디렉터리 생성 및 권한 부여** 

```
# mkdir -p /app/ora19c/19c
# mkdir -p /app/oraInventory
# chown -R ora19c.dba /app/ora19c
# chown -R ora19c.dba /app/oraInventory

# chgrp -R dba /app
# chmod -R 775 /app
# ls -al /app
```

![02](/KH_Security/Linux/Oracle/img/02.png)

- $ORACLE_BASE는 /app/ora19c를 사용하는데 이로 인해 oraInventory 디렉토리가 /app에 만들어짐으로  
ora19c 계정이 /app 디렉토리에 대해서 소유권과 쓰기 권한을 갖도록 설정해야 합니다.

---

### 리눅스 설정

- **hosts 설정**
```
vi /etc/hosts

192.168.10.118 DB19.itclass.co.kr DB19
```

![03](/KH_Security/Linux/Oracle/img/03.png)

- **SELinux 비활성화**
```
vi /etc/selinux/config
```

![04](/KH_Security/Linux/Oracle/img/04.png)

- **방화벽 비활성화 및 SELinux 설정 확인**
```
# sestatus

# systemctl disable --now firewalld.service
# systemctl is-enabled firewalld.service
# systemctl is-active firewalld.service
```

![05](/KH_Security/Linux/Oracle/img/05.png)

---

### 추가 패키지 설치

- Oracle DB는 단독 프로그램이 아니라 다양한 시스템 라이브러리에 의존합니다.  
따라서 설치 전에 필요한 패키지를 미리 설치해야 정상 동작합니다.

```
# dnf -y install ksh libaio-devel glibc-devel libstdc++-devel gcc-c++ libnsl wget
# dnf install -y https://yum.oracle.com/repo/OracleLinux/OL7/latest/x86_64/getPackage/compat-libcap1-1.10-7.el7.x86_64.rpm
# dnf install -y https://yum.oracle.com/repo/OracleLinux/OL7/latest/x86_64/getPackage/compat-libstdc++-33-3.2.3-72.el7.x86_64.rpm
# dnf install -y https://yum.oracle.com/repo/OracleLinux/OL8/appstream/x86_64/getPackage/oracle-database-preinstall-19c-1.0-2.el8.x86_64.rpm
```

---

### ora19c 계정 설정

- 다음의 설정은 ora19c 계정 즉 설치될 오라클의 관리 계정에서 수행해야 하므로 `ora19c` 계정으로 로그인합니다.
- `ORACLE_BASE`, `ORACLE_HOME`, `ORACLE_SID`, `TNS_ADMIN`등은 오라클을 운영하는데 매우 중요한 설정이므로 주의합니다.

![06](/KH_Security/Linux/Oracle/img/06.png)
  
- **Oracle 환경변수 설정 파일 (.bash_profile)**
```
vi ~/.bash_profile
 
# oracle setup
export TMP=/tmp
export TMPDIR=$TMP
export ORACLE_OWNER=ora19c
export ORACLE_BASE=/app/ora19c
export ORACLE_HOME=/app/ora19c/19c
export ORACLE_SID=DB19
export TNS_ADMIN=$ORACLE_HOME/network/admin
export PATH=$PATH:$ORACLE_HOME/bin:$ORACLE_HOME:/usr/bin:.
export NLS_LANG=AMERICAN_AMERICA.AL32UTF8
export ORACLE_HOSTNAME=DB19.itclass.co.kr
export CV_ASSUME_DISTID=RHEL7.6
```

![07](/KH_Security/Linux/Oracle/img/07.png)

- **Oracle 환경변수 설정 확인**
  - Oracle 관련 환경변수가 정상적으로 적용되었는지 확인합니다.
  - 환경변수가 적용되지 않으면 오라클이 정상적으로 실행되지 않을 수 있습니다.
```
env | grep ORACLE
```

![08](/KH_Security/Linux/Oracle/img/08.png)

---

## 설치용 패키지 준비

### 압축 해제

- Linux xWindows에서 진행해야합니다.
- `ora19c` 계정으로 로그인하고 `cd $ORACLE_HOME` 명령어를 통해 해당 디렉터리로 이동해줍니다.

![09](/KH_Security/Linux/Oracle/img/09.png)

- 그 이후 다음과 같이 진행해줍니다.
```
wget http://192.168.10.11/data/down/db/LINUX.X64_193000_db_home.zip
unzip LINUX.X64_193000_db_home.zip
```

### Installer 실행

- 압축 해제 후 파일을 삭제해주고 다음과 같이 진행합니다.
```
unset LANG - 이 설정으로 ORACLE에서 일어나는 언어 충돌 방지합니다.
./runInstaller
``` 

![10](/KH_Security/Linux/Oracle/img/10.png)

---

## Universal Installer

- 단일 데이터베이스를 생성하고 기본 설정까지 자동으로 구성하기 위해 상단 옵션을 선택합니다.

![11](/KH_Security/Linux/Oracle/img/11.png)

---

- 단일 사용자 환경에서 간단한 설치 및 기본 설정 자동 구성을 위해 Desktop class 옵션을 선택합니다.

![12](/KH_Security/Linux/Oracle/img/12.png)

---

- 다음과 같은 설정은 자동으로 설정되어 있어야 합니다.
```
Oracle base : /app/ora19c
Database file location : /app/ora19c/oradata
```

- Global database name은 다음과 같이 변경해줍니다.
```
orcl.itclass.co.kr -> DB19.itclass.co.kr
```

![13](/KH_Security/Linux/Oracle/img/13.png)

- 비밀번호 설정 후 `Next` 버튼을 클릭합니다.

---

- 다음과 같은 화면에서도 `Next` 버튼을 클릭합니다.

![14](/KH_Security/Linux/Oracle/img/14.png)

---

- Oracle 설치 과정에서 시스템 수준의 설정 및 권한 변경이 필요하므로  
root 권한으로 스크립트를 실행하기 위해 비밀번호를 입력합니다.

![15](/KH_Security/Linux/Oracle/img/15.png)

---

- Install 버튼을 클릭해줍니다.

![16](/KH_Security/Linux/Oracle/img/16.png)

---

- 다음 창이 나올 시 Yes를 눌러줍니다.  
Oracle 설치 과정에서 필요한 root 권한 스크립트를 자동으로 실행하여 설치를 완료하기 위해 Yes를 선택해줍니다.

![17](/KH_Security/Linux/Oracle/img/17.png)

- 이 과정을 거친 후 설치가 완료됩니다.

---

## Putty - Oracle 접속

- `slqplus / as sysdba`로 접속을 해줍니다.
- 다음 명령어로 상태 확인합니다.
```
select status from v$instance;
```

![18](/KH_Security/Linux/Oracle/img/18.png)

- status(OPEN) : 데이터베이스 인스턴스가 정상적으로 시작되어 사용자 접근 및 작업이 가능한 상태입니다.

### Oracle 시작 및 끄기

- 다음 명령어들은 필수입니다.
  - startup : Oracle 시작
  - shutdown immediate : Oracle 끄기 (컴퓨터 끄기 전에 반드시 입력)

![19](/KH_Security/Linux/Oracle/img/19.png)

- 처음 시작 시에는 startup인 상태로 시작하며,
Oracle 접속을 끊을 시 위의 결과처럼 나옵니다.
