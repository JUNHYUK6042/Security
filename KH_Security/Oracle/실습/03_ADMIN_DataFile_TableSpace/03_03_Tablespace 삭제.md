# Tablespace 삭제

## 사용 명령어
```sql
  DROP TABLESPACE <tablespace 명>
  [INCLUDING CONTENTS AND DATAFILES CASCADE CONSTRAINTS];
```

- 지정된 Tablespace를 삭제합니다.

### 옵션
- `INCLUDING CONTENT` : Tablespace에 segment가 존재할 때 segment를 같이 삭제합니다.

- `CASCADE CONSTRAINTS` : 삭제되는 tablespace의 table을  
  다른 tablespace의 table이 참조하는 경우에 해당 constraint를 같이 삭제합니다.

- `AND DATAFILES` : Tablespace에 포함된 data file을 같이 삭제합니다.  
  이 옵션을 쓰지 않으면 데이터 파일은 OS상에서 직접 지워야합니다.

---

## 실습
```sql
  DROP TABLESPACE users;
```

![15](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/15.png)

- 현재 USERS Tablespace는 Default Permanent Tablespace로 지정되어 있으므로 삭제가 되지 않습니다.

---

## 비어있는 Tablespace 삭제

- 다음과 같은 명령어로 Tablespace를 삭제합니다.
```sql
  DROP TABLESPACE users;
```

![16](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/16.png)

- 위의 결과처럼 Tablespace는 삭제 되었지만, Datafile은 삭제가 되지 않은 것을 볼 수 있습니다.

- 따라서 다음과 같이 OS 명령어로 직접 삭제해야 합니다.
```
  !rm /app/ora19c/oradata/DB19/insa01.dbf
  !rm /app/ora19c/oradata/DB19/insa02.dbf
  !rm /app/ora19c/oradata/DB19/insa03.dbf

  SELECT tablespace_name, bytes, file_name FROM dba_data_files;
```

![17](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/17.png)

- 삭제 후 datafile 조회한 결과 지워진 것을 볼 수 있습니다.

---

## Tablespace READ ONLY

### 사용 명령어
```
  ALTER TABLESPACE <tablespace명> [ READ ONLY / READ WRITE ]
```

- 지정한 tablespace를 읽기 전용(읽기 쓰기)으로 변경한다

- `READ ONLY` : 데이터 조회만 가능하며 수정, 삭제, 입력은 수행할 수 없습니다.
- `READ WRITE` : 데이터 조회뿐만 아니라 수정, 삭제, 입력까지 모두 수행할 수 있습니다.

---

### 사용자 생성
```sql
  CREATE USER st
  IDENTIFIED BY st
  DEFAULT TABLESPACE users
  QUOTA UNLIMITED ON users;

  GRANT connect, resource TO st;
```

- `CREATE USER` : 새로운 데이터베이스 사용자를 생성합니다.

- `IDENTIFIED BY` : 사용자의 비밀번호를 설정합니다.

- `DEFAULT TABLESPACE` : 기본 테이블스페이스를 `users`로 설정합니다.
  - 사용자가 객체(테이블 등)를 생성하면 이 공간에 저장됩니다.

- `QUOTA UNLIMITED ON` : 테이블스페이스에서 사용할 수 있는 용량을 무제한으로 설정합니다.

![18](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/18.png)

- 사용자 `st`를 생성하고 기본 테이블스페이스를 설정하며 사용 용량을 무제한으로 허용합니다.

- 데이터베이스 접속 및 객체 생성 권한을 부여하여 정상적으로 작업이 가능하도록 설정합니다.

---

### 사용자 조회
```sql
  SELECT owner, table_name, tablespace_name
  FROM dba_tables
  WHERE owner = 'ST'
```

![19](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/19.png)

- `USERNAME`
  - 데이터베이스 사용자 이름이 `ST`입니다.

- `ACCOUNT_STATUS : OPEN`
  - 계정의 현재 상태를 나타이며, 정상적으로 로그인 가능한 상태입니다.
 
- `DEFAULT_TABLESPACE`
  - 사용자의 기본 테이블스페이스가 `USERS` 인것을 의미합니다.
 
---

### 테이블 생성 및 Tablespace 저장 위치 확인

- 다음 명령어로 Table을 생성합니다.
  - `st` 사용자 아래에 `test` 테이블을 생성합니다.
  - 컬럼은 `no NUMBER` 하나로 구성됩니다
```sql
  CREATE TABLE st.test (
  no number
  );
```

- 다음 명령어를 통해 생성한 테이블의 소유자, 이름, 저장된 Tablespace를 조회합니다.
```sql
  SELECT owner, table_name, tablespace_name
  FROM dba_tables
  WHERE owner = 'ST' AND table_name = 'TEST';
```

![20](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/20.png)

- TEST 테이블의 소유자는 ST이며, TEST 테이블은 USERS Tablespace에 저장됩니다.

--- 

### INSERT 및 COMMIT 후 데이터 확인

- 다음 명령어를 통해 TEST 테이블에 값을 삽입합니다.
```sql
  INSERT INTO test
  VALUES (10);
```

![21](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/21.png)

- 다음 명령어로 삽입된 값을 조회합니다.
```sql
  SELECT * FROM st.test;
```

![22](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/22.png)

---

### Tablespace 상태 READ ONLY로 변경

- 다음 명령어를 통해 USERS 테이블스페이스를 READ WRITE 상태에서 READ ONLY 상태로 변경합니다.
```sql
  ALTER TABLESPACE users READ ONLY;
```

- 다음 명령어를 통해 Talbespace를 조회합니다.
```
  SELECT tablespace_name, status, contents, extent_management, segment_space_management
  FROM dba_tablespaces
  ORDER BY 1;
```

![23](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/23.png)

- USERS 테이블스페이스의 상태가 `READ ONLY`로 변경되었습니다.
- USERS 테이블스페이스에 존재하는 테이블은 조회만 가능하며,  
  INSERT, UPDATE, DELETE와 같은 데이터 변경 작업은 수행할 수 없습니다.

---

### READ ONLY Tablespace에서의 데이터 변경(DML) 제한 동작 확인

- **사용자** 접속
```sql
  CONN st/st
```

- INSERT 삽입
```sql
  INSERT INTO test
  VALUES (20);
```

![24](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/24.png)

- DELETE 삭제
```sql
  DELETE FROM test;
```

![25](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/25.png)

- USERS 테이블스페이스가 READ ONLY 상태이므로 데이터 변경(INSERT, DELETE)이 불가능합니다.

![26](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/26.png)

- 하지만 READ ONLY 상태에서도 조회는 가능하기 때문에 정상적으로 수행합니다.

---

- DROP
```sql
  DROP TABLE test;

  SELECT * FROM tab
  WHERE tname = 'TEST';
```

![27](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/27.png)

- DROP은 데이터 변경(DML)이 아니라 데이터 정의(DDL)작업이기 때문에 정상적으로 수행합니다.

---

### Tablespace 상태 변경 후 상태 확인

- sys계정으로 로그인 후 다음 명령어를 통해 users의 Tablespace 상태를 변경합니다.
```sql
  ALTER TABLESPACE users READ WRITE;
```

- **상태 조회**
```sql
  SELECT tablespace_name, status, contents, extent_management, segment_space_management
  FROM dba_tablespaces
  ORDER BY 1;
```

![28](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/28.png)

- `STATUS` : ONLINE
  - 모든 Tablespace가 읽기/쓰기 가능한 상태입니다.
  - 이전에 READ ONLY였던 USERS도 정상적으로 복구되었습니다.
 
- **최종 정리**
  - 모든 Tablespace가 ONLINE 상태로 변경되어 읽기와 쓰기가 모두 가능한 상태이며,    
    이전에 READ ONLY였던 USERS Tablespace도 정상적으로 READ WRITE 상태로 복구되었습니다.
