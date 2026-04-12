# Default Permanent Tablespace

- 사용자 생성 시 별도로 Tablespace를 지정하지 않으면  
  자동으로 할당되는 기본 Tablespace입니다.
- 사용자의 기본 저장 공간을 자동으로 지정하는 Tablespace입니다.

---

## 사용 명령어

```sql
  SELECT * FROM DATABASE_PROPERTIES
  WHERE PROPERTY_NAME = 'DEFAULT_PERMANENT_TABLESPACE';
```

- DB전체에 대해서 정의된 DEFAULT TABLESPACE를 조회합니다.

---

## Tablespaec 생성

```sql
  CREATE TABLESPACE te
  DATAFILE '/app/ora19c/oradata/disk1/te01.dbf' size 5M;
```

![42](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/42.png)

- 사용자를 생성하기 전 Tablespace를 먼저 생성합니다.

---

## 사용자 생성

- 다음 명령어로 사용자 생성합니다.
```sql
  CREATE USER te01
  indentified by te01;
```

![43](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/43.png)

- 데이터베이스 사용자 정보를 조회, 각 사용자의 Tablespace 및 상태를 확인합니다.
```sql
  SELECT username, default_tablespace, temporary_tablespace, account_status, profile
  FROM dba_users
  ORDER BY 1;
```
```
- USERNAME : TE01 
- DEFAULT_TABLESPACE : USERS  
- TEMPORARY_TABLESPACE : TEMP  
- ACCOUNT_STATUS : OPEN  
```

### 용어 정리
```
- DEFAULT TABLESPACE : 사용자의 데이터 저장 기본 공간입니다.  
- TEMPORARY TABLESPACE : 정렬, 임시 작업에 사용됩니다.  
- ACCOUNT_STATUS : 계정의 활성 상태입니다.
```

![44](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/44.png)

- DEFAULT_TABLESPACE : USERS  
- TEMPORARY_TABLESPACE : TEMP  
- ACCOUNT_STATUS : OPEN  

- User 별 Default Talbespace를 지정해주지 않으면
  DB에 지정된 Default Tablespace가 자동으로 지정됩니다.

---

## Default Tablespace 변경

- 다음 명령어로 DB전체에 정의된 Default Tablespace를 te로 변경합니다.
```sql
  ALTER DATABASE DEFAULT TABLESPACE te;
```

![45](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/45.png)

### 용어 정리
```
- DATABASE_PROPERTIES : DB 전체 설정 정보를 저장합니다.  
- DEFAULT_PERMANENT_TABLESPACE : 기본 Tablespace 설정 값입니다.  
```

- 조회한 결과 Default Tablespace가 TE로 바뀌었습니다.
- 이후로 만드는 유저는 자동으로 Default Tablespace인 TE에 저장이됩니다.

---

## Default Tablespace 변경 후 사용자 적용 확인

- te01, te02를 생성 후 Default Tablespace를 확인합니다.
```sql
  SELECT username, default_tablespace
  FROM dba_users
  WHERE username IN ('TE01', 'TE02');
```

![46](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/46.png)

- Default Tablespace 변경 시 기존 사용자와 신규 사용자 모두 TE로 적용됩니다.

---

## 최종 정리

- Default Tablespace를 TE로 변경한 이후 사용자 생성 및 조회를 통해 
  기존 사용자와 신규 사용자 모두 TE Tablespace가 기본 저장 공간으로 적용된 것을 확인하였습니다.
