# Tablespace & DataFile 상태 조회

## 사용 명령어
```sql
SELECT tablespace_name, status, contents,
extent_management, segment_space_management
FROM dba_tablespaces;
```
- Tablespace의 상태를 조회합니다.
- STATUS : 사용 가능 여부를 나타냅니다.
- CONTENTS : 저장 Segment의 종류를 나타냅니다.
- EXTENT_MANAGEMENT : Extent의 할당 및 관리 방식을 나타냅니다.
- SEGMENT_SPACE_MANAGEMENT : Block 내의 공간 관리 방식을 나타냅니다.

```sql
SELECT tablespace_name, bytes, file_name
FROM dba_data_files;
```
- Tablespace별 Data File의 상태를 조회합니다.
- BYTES : Data File의 크기입니다.
- FILE_NAME : Data File의 경로명을 포함한 이름입니다.

```sql
SELECT t.name tablespace_name, d.bytes, d.name file_name
FROM v$tablespace t, v$datafile d
WHERE t.ts# = d.ts#;
```
- Tablespace별 Data File의 상태를 조회합니다.
- Dictionary가 아니라 Dynamic Performance View를 조회하는 것이므로 MOUNT 상태에서도 조회 가능합니다.

---

## Tablespace 상태 조회

- 다음 명령어로 테이블스페이스의 상태를 조회합니다.
```sql
  SELECT tablespace_name, status, contents, extent_management, segment_space_management
  FROM dba_tablespaces
  ORDER BY 1;
```

![01](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/01.png)

- `TABLESPACE_NAME` : 테이블스페이스 이름입니다.
  - `SYSTEM` : Oracle 핵심 딕셔너리가 저장되는 테이블스페이스입니다.
  - `SYSAUX` : SYSTEM의 보조 역할을 하며, AWR, 통계 정보 등 여러 보조 구성요소를 저장합니다.
  - `TEMP` : 정렬, 조인 등 임시 작업 공간입니다.
  - `UNDOTBS1` : 트랜잭션 롤백용 Undo 데이터를 저장합니다.
  - `USERS` : 일반 사용자의 기본 테이블스페이스입니다.

---

- `STATUS` : 테이블스페이스의 현재 상태를 나타냅니다.
  - `ONLINE` : 정상적으로 사용 가능한 상태입니다.
  - `OFFLINE` : 사용 불가능한 상태입니다.
  - `READ ONLY` : 조회만 가능하고 변경은 불가능한 상태입니다.

 ---

- `CONTENTS` : 테이블스페이스의 용도를 나타냅니다.
  - `PERMANENT` : 일반 테이블, 인덱스 등 영구 데이터입니다.
  - `TEMPORARY` : 세션이나 작업 중 임시로 사용하는 데이터입니다.
  - `UNDO` : 트랜잭션 롤백 및 복구용입니다.

---

- `EXTENT_MANAGEMENT` : Extent(공간 할당 방식)의 관리 방법을 나타냅니다.
  - `LOCAL` : 테이블스페이스 내부에서 Extent를 관리합니다. (현재 표준)
  - `DICTIONARY` : 데이터 딕셔너리에서 관리합니다. (구식 방식)

---

- `SEGMENT_SPACE_MANAGEMENT` : Segment 내부의 공간 관리 방식을 나타냅니다.
  - `AUTO` : Oracle이 자동으로 관리합니다.
  - `MANUAL` : 사용자가 직접 관리합니다.
 
---

## Datafile 상태 조회

- 다음 명령어로 DataFile의 상태를 조회합니다.
```sql
  SELECT tablespace_name, bytes, file_name
  FROM dba_data_files;
```

![02](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/02.png)

- `TABLESPACE_NAME` : 테이블스페이스 이름입니다.

- `BYTES` : 데이터파일의 크기를 바이트(Byte) 단위로 나타냅니다.
  - 실제 디스크에 할당된 용량입니다.
 
- `FILE_NAME` : 데이터파일의 물리적인 경로와 파일명을 나타냅니다.
  - OS 레벨에서 존재하는 실제 파일 경로입니다.
  - 장애 발생 시 위치 파악에 매우 중요합니다.
 
---

## Tempfile 상태 조회
- 다음 명령어로 DataFile의 상태를 조회합니다.
```sql
  SELECT tablespace_name, bytes, file_name
  FROM dba_temp_files;
```

![03](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/03.png)

- 컬럼의 뜻은 Datafile의 컬럼과 같은 뜻입니다.

--- 

## v$datafile 조회

- v$datafile은 Oracle이 실시간으로 관리하는 데이터파일 정보를 담고 있는 Dynamic Performance View입니다.

- 다음 명령어로 테이블스페이스-파일-경로의 연결을 간단하게 확인할 수 있습니다.
```sql
  SELECT ts#, file#, name
  FROM v$datafile;
```

![04](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/04.png)

### 각 컬럼 의미

#### `TS#`
- 데이터파일이 속한 **테이블스페이스 번호**를 나타냅니다.
- 숫자 자체만으로는 의미를 알기 어렵고, `v$tablespace`와 함께 봐야 합니다.

#### `FILE#`
- 데이터파일 번호를 나타냅니다.
- Oracle이 각 데이터파일을 구분하기 위해 부여한 번호입니다.
- 파일 이름이 바뀌더라도 내부적으로는 이 번호로 식별하는 경우가 많습니다.

#### `NAME`
- 데이터파일의 **실제 OS 경로와 파일명**을 나타냅니다.
- 즉, 물리적으로 어디에 저장되어 있는 파일인지 보여줍니다.

---

## v$tablespace 조회

- 다음 명령어로 테이블스페이스 번호와 이름을 확인할 수 있습니다.
```sql
  SELECT ts#, name
  FROM v$tablespace;
```

![05](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/05.png)

### 각 컬럼 의미

#### `TS#`
- 테이블스페이스 번호를 나타냅니다.
- `v$datafile`의 `TS#`와 연결되는 기준값입니다.

#### `NAME`
- 테이블스페이스 이름을 나타냅니다.
- 사람이 실제로 구분할 때 사용하는 논리적 이름입니다.

- TEMP가 보인다는 것은 TEMP 테이블스페이스가 존재한다는 것을 알려주지만, Temp File 경로까지 같이 보인다는 뜻은 아닙니다.
- Temp File 경로를 보려면 v$tempfile 또는 dba_temp_files를 조회해야 볼 수 있습니다.

---

## Mount 상태에서 조회

- 마지막으로, MOUNT 상태 이상에서는 다음과 같이 조회할 수 있습니다.
```sql
  SELECT t.name tablespace_name, d.bytes, d.name file_name
  FROM v$tablespace t, v$datafile d
  WHERE t.ts# = d.ts#
  ORDER BY 1;
```

![06](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/06.png)

- Temp File까지 같이 보기위해 별도로 v$tempfile을 조회하려면 다음과 같이 명령어를 입력합니다.
```sql
  SELECT t.name tablespace_name, f.bytes, f.name file_name
  FROM v$tablespace t, v$tempfile f
  WHERE t.ts# = f.ts#;
```
