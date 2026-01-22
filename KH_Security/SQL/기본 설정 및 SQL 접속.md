# SQL 실습을 위한 기본 설정 및 접

- 다음 경로들은 반드시 알고 있어야 합니다.

```
ORACLE_BASE -> C:\app\ora19c
ORACLE_HOME -> C:\app\ora19c\client
TND_ADMIN -> C:\app\ora19c\client\network\admin
```

---

## Oracle Database Client 설치

### 설치 유형 선택
- 다음과 같이 `런타임`을 선택 후 `다음`을 눌러줍니다.

![01](/KH_Security/SQL/img/01.png)

---

### Oracle 사용자 홈 선택

- 다음 버튼을 누릅니다.

![02](/KH_Security/SQL/img/02.png)

---

### 설치 위치 지정

#### Oracle 기본 위치 경로 (ORACLE_BASE)

```
C:\app\ora19c
```

#### Oracle 소프트웨어 위치 (ORACLE_HOME)

```
C:\app\ora19c\client
```

![03](/KH_Security/SQL/img/03.png)

---

### 요약

- 다음과 같이 특별한 표시가 없으면 `설치` 버튼을 눌러줍니다.

![04](/KH_Security/SQL/img/04.png)

---

### 설치 완료

![05](/KH_Security/SQL/img/05.png)

- 진행이 다 완료되면 설치 끝입니다.

---

## 파일 구성

### TNS_ADMIN

- 경로
```
C:\app\ora19c\client\network\admin
```

- 위의 경로 밑에 `tnsnames.ora` 파일을 넣어줍니다.

![06](/KH_Security/SQL/img/06.png)

### sc.sql & school.sql

- 인코딩 정보  
```
school.sql : UTF-8
sc.sql : ANSI
```

![07](/KH_Security/SQL/img/07.png)

- 해당 SQL 파일들은 Windows 사용자 홈 디렉터리(`C:\Users\ast06`)에 저장되어 있으며,  
로컬 데이터베이스 실습 및 SQL 테스트 용도로 사용되었습니다.

---

## TNS 연결 테스트

- Oracle 데이터베이스 접속을 확인하기 위해 `tnsping` 명령어를 사용했습니다.

```
tnsping dal
```

![08](/KH_Security/SQL/img/08.png)

### 실행 결과
- TNS Ping이 정상적으로 응답하였으며,  
Oracle 데이터베이스 서버와의 네트워크 연결이 정상임을 확인했습니다.

---

## SQL*Plus 접속 테스트

- 제 계정으로 접속하기 위해 다음과 같이 입력했습니다.

```
sqlplus ast06/ast06@dal
```

![09](/KH_Security/SQL/img/09.png)

> tnsping을 통해 네트워크 연결을 확인한 후  
SQL*Plus를 사용하여 Oracle 19c 데이터베이스에 정상 접속함

---
