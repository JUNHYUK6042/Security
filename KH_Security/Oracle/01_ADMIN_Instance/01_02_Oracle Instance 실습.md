# Oracle Instance 실습

---

## 사용자 계정 활성화 및 비밀번호 설정
```
  ALTER USER hr
  IDENTIFIED BY hr \\ 비밀번호 변경
  ACCOUNT unlock; \\ 계정 잠금을 해제 
```

![02](/KH_Security/Oracle/imgs/01_오라클%20기초/02.png)

### 계정 접속
```sql
  sqlplus hr/hr
```

![03](/KH_Security/Oracle/imgs/01_오라클%20기초/03.png)

- 다른 세션을 열어 sqlplus 툴을 사용하요 hr 사용자 계정에 접속합니다.

---

## SHUTDOWN 옵션 동작 실습

- **관리자 계정**
```sql
  shutdown normal

  shutdown transactional
```

- **사용자(hr) 계정**
```sql
  select * from tab;

  exit
```

![04](/KH_Security/Oracle/imgs/01_오라클%20기초/04.png)

- `shutdown normal` : 현재 접속중인 사용자가 있기에 종료가 안되고 대기하는 것을 알 수 있습니다.
- `shutdown transactional` : 새로운 접속을 차단과 동시에 트랜잭션을 종료 후 DB 종료를 하였습니다.

---

### SHUTDOWN Transactional 실습

- **사용자 계정**
```sql
  desc employees;

  UDATE employees SET salary = salary * 1.2;
```

![05](/KH_Security/Oracle/imgs/01_오라클%20기초/05.png)

- **관리자 계정**
  - `SHUTDOWN TRANSACTIONAL` 명령어를 입력해 줍니다.
  - 이때 사용자 계정에서는 commit이나 rollback을 해주지 않아서 트랙잭션이 아직 진행 중이어서
  DB를 종료시키지 않고 끝날 때까지 대기 상태가 됩니다.

![06](/KH_Security/Oracle/imgs/01_오라클%20기초/06.png)

- 다른 세션을 열어 사용자 계정 로그인 시도를 해보지만 트랜잭션이 끝나지 않아 DB 종료가 되지 않아서
새로운 접속을 하지 못하게 막습니다.

![07](/KH_Security/Oracle/imgs/01_오라클%20기초/07.png)

- 사용자 계정에서 rollback 명령어를 통해 트랙잭션을 종료하면 진행 중이던 트랜잭션이 종료되어
관리자 계정에서 DB가 정상적으로 종료된 것을 확인할 수 있습니다.

---

### SHUTDOWN Transactional 후 사용자 세션 영향

- **관리자 계정**
```sql
  SHUTDOWN TRANSACTIONAL
```

- **사용자 계정**
```sql
  SELECT * FROM tab;

  CONNECT hr/hr
```

![08](/KH_Security/Oracle/imgs/01_오라클%20기초/08.png)

- 관리자 세션에서 `SHUTDOWN TRANSACTIONAL` 명령 실행 후 트랜잭션이 종료되면서 데이터베이스가 정상적으로 종료됩니다.
- 사용자 세션에서는 데이터베이스 인스턴스가 완전히 종료되었기 때문에 SQL문 실행이 불가합니다.
- 위의 결과처럼 새로운 접속도 불가능합니다.

---

## Readonly 상태의 이해와 실습

- 관리자세션으로 접속한 뒤 `shutdown immediate` 명령어를 통해 강제 종료합니다.

![09](/KH_Security/Oracle/imgs/01_오라클%20기초/09.png)

- 그 이후 startup open read only 명령어를 입력하여 종료 상태의 DB를 읽기 전용으로 OPEN합니다.

```sql
  startup mount \\ mount단계로 시작합니다.

  alter database open read only; \\ 데이터 파일을 읽기 전용으로 오픈합니다.

  select open_mode from v$database; \\ 현재 데이터베이스가 read only 모드인지 확인합니다.
```

![10](/KH_Security/Oracle/imgs/01_오라클%20기초/10.png)

- **사용자 계정**
  - 사용자 계정으로 접속 후 다음과 같은 명령어를 입력합니다.
```sql
  SELECT count(*) FROM employees;
```

![11](/KH_Security/Oracle/imgs/01_오라클%20기초/11.png)

- READ ONLY 상태에서는 데이터 조회(SELECT)는 허용되므로 정상적으로 실행됩니다.

```sql
  UPDATE employees SET salary = salary * 1.2;
```

![12](/KH_Security/Oracle/imgs/01_오라클%20기초/12.png)

- UPDATE 문은 데이터 변경 작업(DML)이므로 READ ONLY 상태에서는 실행할 수 없으며 오류가 발생합니다.

---

## Sessioin 확인 & 사용자의 강제 종료

### 명령어
```
- ALTER SYSTEM [ ENABLE | DISABLE ] RESTRICTED SESSION;
  - 제한 모드 상태를 활성화/비활성화 합니다.

- SELECT LOGINS FROM V$INSTANCE;
  - 제한 모드의 활성화 상태를 조회합니다.
  - restricted : 제한 모드 상태, allowed : 일반모드 상태를 나타냅니다.

- SELECT SID, SERIAL#, USERNAME, STATUS FROM V$SESSION;
  - DB에 접속 중인 세션을 확인합니다.
  - SID(고유번호) : 각 접속마다 하나씩 부여합니다.
  - SERIAL# : 세션을 유일하게 식별하는 값이며, 같은 SID가 재사용될 수 있기 때문에 같이 사용합니다.

- ALTER SYSTEM KILL SESSION <'SID번호, SERIAL번호'>;
  - 지정한 세션을 강제 종료합니다.

- SELECT * FROM DBA_SYS_PRIVS;
  - 사용자에게 부여된 시스템 권한 정보를 조회합니다.

- GRANT <시스템 권한> TO <user명>;
  - 권한을 할당합니다.

- REVOKE <시스템 권한> FROM <user명>;
  - 권한을 해제합니다.
```

---

### 실습
```sql
  SELECT logins FROM v$instance;
```

![13](/KH_Security/Oracle/imgs/01_오라클%20기초/13.png)

- 제한모드 활성화 상태가 `ALLOWED` 상태이므로 데이터베이스는 정상적으로 모든 사용자 접속을 허용하고 있습니다.

#### RESTICTED 설정

- 제한모드를 RESTRICTED 상태로 변경한 후 로그인 상태를 확인합니다.
```sql
  ALTER SYSTEM ENABLE RESTRICTED SESSION;

  SELECT LOGINS FROM v$instance;
```

- 다음과 같이 로그인 상태가 RESTRICTED인 것을 알 수 있습니다.

![14](/KH_Security/Oracle/imgs/01_오라클%20기초/14.png)

- 사용자 세션에서 SELECT문을 실행해보았습니다.

![15](/KH_Security/Oracle/imgs/01_오라클%20기초/15.png)

- 또 다른 사용자 세션에서 접속을 시도해보았습니다.

![16](/KH_Security/Oracle/imgs/01_오라클%20기초/16.png)

- 로그인 상태를 제한모드인 RESTRICTED 상태입니다.
따라서, 새 사용자 세션의 접속을 제한했으므로 접속이 불가능하게 됩니다.

---

#### 접속 중인 세션 확인

- 관리자 세션에서 SELECT 문을 실행합니다.
```sql
  SELECT sid, serial$, username, status FROM v$session
  WHERE privilege LIKE '%RESTRICTED%';
```

![17](/KH_Security/Oracle/imgs/01_오라클%20기초/17.png)

- 위의 결과와 같이 접속 중인 세션은 HR 사용자인 것을 확인할 수 있고,  
SID는 256, SERIAL#은 45096인 것을 확인할 수 있습니다.

---

```sql
  ALTER SYSTEM KILL SESSION '256,45096';
```

- SID가 256이고, Serial#이 45096인 세션을 강제로 종료합니다.

![18](/KH_Security/Oracle/imgs/01_오라클%20기초/18.png)

- 이미 접속 중인 세션에서 SQL 문이 실행해도 강제로 종료했기 때문에 SQL 문이 실행되지 않습니다.

---

- `DBA_SYS_PRIVS`에서 PRIVILEGE 컬럼에 RESTRICT라는 문자열이 포함된 권한만 조회합니다.
```sql
  SELECT * FROM dba_sys_privs
  WHERE privilege LIKE '%RESTRICT%';
```

![19](/KH_Security/Oracle/imgs/01_오라클%20기초/19.png)

- DBA와 SYS 계정은 `RESTRICTED`상태에서도 접속이 가능합니다.

---

- 다음과 같은 명령어로 hr 계정에도 `RESTRICTED SESSION` 모드에서도 접속이 가능하도록 권한을 부여해줍니다.
```sql
  GRANT restricted session TO HR;
```
  
![20](/KH_Security/Oracle/imgs/01_오라클%20기초/20.png)

- 다시 권한이 부여된 계정을 조회했을 때 HR 계정도 포함이된 것을 알 수 있습니다.

![21](/KH_Security/Oracle/imgs/01_오라클%20기초/21.png)

---

- `RESTRICTED SESSION` 모드에서 접속이 가능하도록하는 권한을 해제 합니다.
- 특정 사용자(HR)에게 부여된 제한 접속 권한을 제거합니다.
```sql
  REVOKE RESTRICTED SESSION FROM HR;
```

- 다음 명령어는 제한 모드 상태를 해제합니다.
```sql
  ALTER SYSTEM DISABLE RESTRICTED SESSION;
```

- 다음 명령어는 접속 상태를 확인합니다.
```sql
  SELECT logins FROM v$instance; 
```

![22](/KH_Security/Oracle/imgs/01_오라클%20기초/22.png)

- 권한 해제한 뒤 제한 모드상태도 비활성화 했으므로 정상적으로 모든 사용자가 접속할 수 있다는 것을 알 수 있습니다.
