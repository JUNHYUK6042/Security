# Tablespace 생성 및 확장

## Tablespace 생성 명령어
```sql
  CREATE TABLESPACE <tablespace명>
  DATAFILE '<data>' SIZE <크기>;
```
- 오라클 10g 이후 버전에서 사용자용 tablespace생성
- Extent management 는 locally 방식으로, segment space management는 AUTO 방식으로 생성된다.

---

```sql
  CREATE TABLESPACE <tablespace명>
  DATAFILE '<data file>' SIZE <크기>
  SEGMENT SPACE MANAGEMENT AUTO;
```
- Oracle 9i 버전에서 사용자용 Tablespace 생성  
- Extent Management는 기본적으로 LOCAL  
- Segment Space Management는 기본값이 MANUAL이므로 반드시 AUTO로 지정해야 함  

---

```sql
  CREATE TABLESPACE <tablespace명>
  DATAFILE '<data file>' SIZE <크기>
  EXTENT MANAGEMENT LOCAL;
```
- Oracle 8i 버전에서 사용자용 Tablespace 생성
- Extent Management 기본값이 DICTIONARY 방식이므로 LOCAL을 명시해야 함

---

## Tablespace 생성 실습

- 다음 명령어를 통해 Tablespace를 생성합니다.
```
  CREATE TABLESPACE insa
  DATAFILE '/app/ora19c/oradata/DB19/insa01.dbf' SIZE 1M;
```

- Tablespace 생성 후 Datafile 상태를 조회합니다.

![07](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/07.png)

- 각 테이블스페이스에 속한 데이터파일 정보를 조회합니다.
- 파일 크기와 경로를 함께 확인할 수 있습니다.

---

## Tablespace 상태 조회

- 다음 명령어로 Tablespace 상태와 관리 방식을 확인합니다.
```sql
  SELECT tablespace_name, status, contents, extent_management, segment_space_management
  FROM dba_tablespaces
  ORDER BY 1;
```
![08](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/08.png)

### 결과 해석

- `STATUS` : ONLINE
  - 모든 테이블스페이스가 정상적으로 사용 가능한 상태입니다.
 
- `EXTENT_MANAGEMENT` : LOCAL
  - 모든 Tablespace가 내부적으로 Extent를 관리합니다.
  - 최신 Oracle에서 기본 방식입니다
 
- CONTENTS 기준 해석
  - PERMANENT (INSA, SYSAUX, SYSTEM, USERS)
    - 일반 데이터 저장용 테이블스페이스입니다.
    - 테이블, 인덱스 등이 저장됩니다.

  - TEMPORARY (TEMP)
    - 정렬, 해시, 임시 작업에 사용됩니다.
    - 실제 영구 데이터는 저장되지 않습니다.

  - UNDO (UNDOTBS1)
    - 트랜잭션 롤백 및 복구에 사용됩니다.

---

## Tablespace 수동 확장

- 다음 명령어를 통해 Tablespace를 수동 확장합니다.
- 지정된 파일의 크기를 늘립니다.
```sql
  ALTER DATABASE DATAFILE
  '<data file>' RESIZE <크기>;
```

- 다음 명령어를 통해 Data File을 추가합니다.
```sql
  ALTER TABLESPACE <tablespace명>
  ADD DATAFILE '<data file>' SIZE <크기>;
```

---

### Tablespace 확장

- 확장 전 DataFile 조회
```sql
  SELECT tablespace_name, bytes, file_name FROM dba_data_files; 
```

- 확장 명령어
```
  ALTER DATABASE DATAFILE
  '/app/ora19c/oradata/DB19/insa01.dbf' RESIZE 2M;
```

- 확장 후 DataFile 조회
```sql
  SELECT tablespace_name, bytes, file_name FROM dba_data_files;
```

![09](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/09.png)

- INSA Tablespace의 크기가 증가한 것을 확인할 수 있습니다.

---

### Datafile 추가

- Datafile 추가 명령어
```
  ALTER TABLESPACE insa
  ADD DATAFILE '/app/ora19c/oradata/DB19/insa02.dbf' SIZE 2M;
```

![10](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/10.png)

- 같은 Tablespace에 Datafile이 추가된 것을 확인할 수 있습니다.

---

## Tablespace 자동 확장

### 사용 명령어
```sql
CREATE TABLESPACE <tablespace명>
DATAFILE '<data file>' SIZE <크기>
AUTOEXTEND ON [NEXT <크기> MAXSIZE <크기>];
```
- 자동으로 커지는 data file을 갖는 tablespace를 생성합다.
- NEXT : 증가치
- MAX SIZE : 최대 크기   

```sql
ALTER TABLESPACE <tablespace명>
ADD DATAFILE '<data file>' SIZE <크기>
AUTOEXTEND ON [NEXT <크기> MAXSIZE <크기>];
```
- tablespace에 자동으로 커지는 data file을 추가 합니다.

```sql
ALTER DATABASE DATAFILE '<data file>'
AUTOEXTEND ON | OFF [NEXT <크기> MAXSIZE <크기>];
```
- 지정한 data file을 자동증가를 설정합니다.

---

### Tablespace 자동 확장 실습

#### Tablespace 생성 & Autoextend 설정
```sql
  CREATE TABLESPACE usr
  DATAFILE '/app/ora19c/oradata/DB19/usr01.dbf' SIZE 2M AUTOEXTEND ON,
  '/app/ora19c/oradata/DB19/usr02.dbf' SIZE 2M AUTOEXTEND ON NEXT 5M,
  '/app/ora19c/oradata/DB19/usr03.dbf' SIZE 2M AUTOEXTEND ON NEXT 5M MAXSIZE 20M;
```

#### Datafile 확인
```sql
  SELECT tablespace_name, bytes, file_name
  FROM dba_data_files;
```

![11](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/11.png)

- 자동으로 증가하는 autoextend 설정은 datafile 별로 따로 설정합니다.

---

#### Autoextend 설정 확인
```sql
  SELECT tablespace_name, file_name, autoextensible, increment_by, maxbytes
  FROM dba_data_files
  WHERE tablespace_name = 'USR';
```

![12](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/12.png)

##### 결과 해석

- `usr01.dbf`
  - AUTOEXTENSIBLE = YES  
  - INCREMENT_BY = 1 (8KB 단위 증가)
  - 너무 작은 증가 단위 → 비효율  

- `usr02.dbf`  
  - AUTOEXTENSIBLE = YES  
  - INCREMENT_BY = 640 (약 5MB 증가)
  - 적절한 설정  

- `usr03.dbf` 
  - AUTOEXTENSIBLE = YES  
  - MAXBYTES = 20MB 제한  
  - 금방 용량 부족 가능

---

### Tablespace AUTOEXTEND Datafile 추가
```sql
  ALTER TABLESPACE insa
  ADD DATAFILE '/app/ora19c/oradata/DB19/insa03.dbf'
  SIZE 2M AUTOEXTEND ON NEXT 5M MAXSIZE 20M;

  SELECT tablespace_name, file_name, autoextensible, increment_by, maxbytes
  FROM dba_data_files
  WHERE lower(tablespace_name) = 'insa';
```

![13](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/13.png)

---

### Datafile AUTOEXTEND 변경
```sql
  ALTER DATABASE DATAFILE '/app/ora19c/oradata/DB19/insa01.dbf'
  AUTOEXTEND ON;

  SELECT tablespace_name, file_name, autoextensible, increment_by, maxbytes
  FROM dba_data_files
  WHERE lower(tablespace_name) = 'insa';
```

![14](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/14.png)
