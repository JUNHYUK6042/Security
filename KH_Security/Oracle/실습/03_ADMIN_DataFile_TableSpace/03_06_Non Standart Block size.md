# Non Standard Block Size 이용한 tablespace 생성 & 삭제

- 기본 DB 블록 크기(예: 8K)가 아닌 다른 크기(2K, 4K, 16K 등)를 사용하는 Tablespace입니다.
- Non Standard Block Size Tablespace를 생성하기 위해서는   
  먼저 해당 Block Size를 처리할 수 있는 Buffer Cache가 존재해야 합니다.
- Tablespace 생성 전에 Buffer Cache 준비가 먼저입니다.

---

## 사용 명령어

### 기본 Buffer 영역 확인
```sql
  SELECT NAME, BYTES 
  FROM V$SGASTAT
  WHERE NAME LIKE '%buffer%' AND POOL IS NULL;
```

- database buffer cache나 redo log buffer 의 크기를 확인합니다.
- 실제 데이터 처리에 사용되는 메모리 영역입니다.

---

### SGA Pool 영역 확인
```sql
  SELECT POOL, SUM(BYTES) 
  FROM V$SGASTAT
  WHERE POOL IS NOT NULL
  GROUP BY POOL;
```

- Shared Pool, Large Pool, Java Pool 크기를 확인합니다.
- DB 메모리 구조 전체를 이해하기 위해 필요합니다.
- V$SGASTAT : SGA 메모리 상태를 확인합니다.


---

### Buffer Cache 상세 확인
```sql
  SELECT NAME, BLOCK_SIZE, BUFFERS, BUFFERS*BLOCK_SIZE AS "SIZE"
  FROM V$BUFFER_POOL;
```

- 다양한 database buffer cache의 상태를 확인합니다.
- 현재 DB가 어떤 block size를 지원하는지 확인합니다.
- Buffer Cache : 데이터를 메모리에 저장하는 영역입니다.  
- Block Size : 데이터 저장 단위 크기입니다.  
- V$BUFFER_POOL : Block Size별 Buffer Cache 상태를 확인합니다.

---

### Tablespace 생성
```sql
  CREATE TABLESPACE <tablespace명>                                         
  DATAFILE '<data file>' SIZE <크기>
  BLOCKSIZE <크기>; 
```

- BLOCKSIZE에 지정된 크기의 block size를 갖는 tablespace를 생성합니다.

---

## Block Size 확인
```sql
  SHOW PARAMETER db_block_size
```

![47](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/47.png)

- 현재 DB의 기본 Block Size는 **8192 byte (8KB)** 입니다.
- 모든 기본 Tablespace는 이 Block Size를 기준으로 생성됩니다.

---

## Tablespace별 Block Size 확인

- 다음 명령어로 각 Tablespace의 Block Size를 조회합니다.
```sql
  SELECT tablespace_name, block_size
  FROM dba_tablespaces; //Tablespace 정보를 조회하는 데이터 딕셔너리
```

![48](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/48.png)

- 모든 Tablespace의 Block Size가 **8192 byte (8KB)** 입니다.
- 현재 데이터베이스는 **기본 Block Size만 사용 중인 상태**입니다.

---

## SGA 상태 확인

- 다음 명령어로 SGA 상태를 확인합니다.
```sql
  SHOW SGA;
```

![49](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/49.png)

- `Fixed Size` : Oracle 내부 동작을 위해 항상 고정적으로 사용하는 메모리 영역입니다.
- `Variable Size` : Shared Pool, Large Pool 등이 포함되며 SQL 실행 정보,  
                    데이터 딕셔너리 등을 저장하는 가변 메모리 영역입니다.
- `Database Buffers` : 데이터 블록을 메모리에 캐싱하여 디스크 I/O를 줄이는 Buffer Cache 영역입니다. 
- `Redo Buffers` : 데이터 변경 사항을 기록하여 장애 발생 시 복구에 사용되는 로그 버퍼 영역입니다.

### 중요한 점

- Non Standard Block Size Tablespace가 존재하는지 확인할 수 있습니다.
- Buffer Cache 추가 필요 여부를 판단할 수 있습니다.

---

## SGA 파라미터 확인

- 다음 명령어로 SGA의 파라미터 값을 확인합니다.
```sql
  SHOW PARAMETER sga_;
```

![50](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/50.png)

- SGA의 크기 및 관리 방식을 확인합니다.
- 메모리가 자동으로 관리되는지, 수동으로 설정되어 있는지 판단합니다.

- `sga_max_size` : SGA가 사용할 수 있는 최대 메모리 크기입니다.  
- `sga_target` : SGA 메모리를 자동으로 분배하는 기준 값입니다.  
- `sga_min_size` : SGA의 최소 크기입니다.

### 중요한 점

- Buffer Cache 크기 설정과 직접 연결됩니다.  
- Block Size Tablespace 생성 시 필요한 메모리 확보 여부를 판단할 수 있습니다. 

---

## Buffer Cache 파라미터 확인

- 다음 명령어를 통해 Block Size별 Buffer Cache 설정 값을 확인합니다.
```sql
  SHOW PARAMETER cache_size;
```

![51](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/51.png)

- 현재 데이터베이스는 **기본 Block Size(8K)만 사용 가능하고, 별도의 Buffer Cache는 존재하지 않습니다.**
- 즉, Non Standard Block Size (2K, 4K, 16K 등)는 사용할 수 없는 상태입니다.
- 기본 Block Size(8K)는 별도 설정 없이 사용됩니다.
- 나머지 Block Size는 직접 cache_size를 설정해야 합니다.
- 설정하지 않으면 Tablespace 생성 시 에러 발생합니다.

### 중요한 점

- Block Size Tablespace 생성 가능 여부를 결정합니다.
- 해당 값이 0이면 해당 Block Size는 사용 불가능합니다.

---

## SGA 및 Buffer Cache 상태 종합 확인

### Pool 영역 확인

- 다음 명령어로 SGA의 논리적 작업 영역 크기를 확인합니다.
```sql
  SELECT pool, SUM(bytes)
  FROM v$sgastat
  WHERE pool IS NOT NULL
  GROUP BY pool;
```

![52](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/52.png)

- Shared Pool : SQL, 실행 계획 저장  
- Java Pool : Java 실행 메모리  
- Large Pool : 대용량 작업용 메모리

---

### Buffer 영역 확인

- 다음 명령어를 통해 데이터 처리와 복구를 위한 핵심 메모리 영역 조회합니다.
```sql
  SELECT name, bytes
  FROM v$sgastat
  WHERE name LIKE '%buffers%' AND pool IS NULL;
```

![53](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/53.png)

- buffer_cache : 데이터 블록 저장 영역  
- log_buffer : 변경 로그 저장 영역

---

### Buffer Pool 상태 확인

- 다음 명령어를 통해 현재 사용 중인 Buffer Cache의 Block Size와 상태를 조회합니다.
```sql
  SELECT name, block_size, buffers, buffers*block_size AS "SIZE"
  FROM v$buffer_pool;
```

![54](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/54.png)

- 현재 데이터베이스에서 사용 중인 Buffer Cache의 종류와 Block Size를 확인합니다.
- 조회 결과 DEFAULT Buffer Cache만 존재하며, Block Size는 8K로 설정되어 있습니다.

---

## Non Standard Block Size Tablespace 생성

- 다음 명령어를 통해 16K Block Size를 사용하는 Tablespace를 생성합니다.
```sql
  CREATE TABLESPACE imsy
  DATAFILE '/app/ora19c/oradata/disk1/imsy01.dbf' SIZE 5M
  BLOCKSIZE 16K;
```

![55](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/55.png)

- 현재 데이터베이스는 기본 Block Size(8K)만 사용할 수 있는 상태입니다.
- 그래서 16K Block Size를 처리할 Buffer Cache가 설정되어 있지 않아 Tablespace 생성이 실패합니다.

---

### Buffer cache 설정

- 다음 명령어를 통해 16K Block Size를 위한 Buffer Cache를 생성합니다.
```sql
  ALTER SYSTEM SET db_16k_cache_size = 16M;
```

- 다음 명령어를 통해 Buffer Cache 설정 확인를 확인합니다.
```sql
  SHOW PARAMETER cache_size;
```

![56](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/56.png)

- `db_16k_cache_size = 16M` 으로 설정된 것을 확인할 수 있습니다.
- 기존에는 0이었으나, 이제 16K Block Size 사용이 가능한 상태입니다.

---

### Buffer Pool 상태 확인

```sql
SELECT name, block_size, buffers, buffers*block_size AS "SIZE"
FROM v$buffer_pool;
```

![57](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/57.png)

- 기존 8K Buffer Cache와 함께  
- 16K Buffer Cache가 새롭게 생성된 상태입니다.
- 이제 8K와 16K Block Size를 모두 사용할 수 있습니다.

---

### Buffer Cache 설정 후 Tablespace 생성

```sql
CREATE TABLESPACE imsy
DATAFILE '/app/ora19c/oradata/disk1/imsy01.dbf' SIZE 5M
BLOCKSIZE 16K;
```

![58](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/58.png)

- 16K Block Size를 사용하는 Tablespace가 정상적으로 생성되었습니다.
- 이전에는 Buffer Cache가 없어 실패했지만,  
  `db_16k_cache_size` 설정 이후 생성이 가능해진 상태입니다.

---

### Tablespace Block Size 확인

- 다음 명령어를 통해 Tablespace의 정보를 데이터 딕셔너리에서 조회합니다.
```sql
  SELECT tablespace_name, block_size
  FROM dba_tablespaces
  ORDER BY 1;
```

![59](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/59.png)

- IMSY Tablespace만 **16K Block Size(16384)**를 사용합니다.
- 나머지 Tablespace는 모두 **기본 8K Block Size(8192)**를 사용합니다.
- Non Standard Block Size Tablespace가 정상적으로 생성되었는지 확인할 수 있습니다.

---

### Datafile 정보 조회

```sql
  SELECT tablespace_name, bytes, file_name
  FROM dba_data_files;
```

![60](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/60.png)

- 각 Tablespace가 어떤 Datafile과 연결되어 있는지 확인할 수 있습니다.
- Datafile의 실제 저장 위치(디렉터리)와 크기를 확인할 수 있습니다.

---

### Buffer Cache 제거 및 Data File 조회

- 다음 명령어를 통해 16K Block size를 사용할 수 있는 Buffer Cache를 설정합니다.
  - 에러가 나지 않지만 16K block 크기를 사용하는 tablespace가 사용할 수 없게 됩니다.
```sql
  ALTER SYSTEM SET db_16k_cache_size = 0M;
```

- Buffecahe 설정 후 Data File 조회합니다.
```sql
  SELECT tablespace_name, bytes, file_name
  FROM dba_data_files;
```

![61](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/61.png)

- 16K Block Size Tablespace(IMSY)는 존재하지만  
- 16K Buffer Cache를 0으로 설정하여 해당 Block Size를 처리할 메모리가 사라진 상태입니다.
- 16K Tablespace는 있는데 16K Buffer Cache가 없어 접근이 불가능한 상태입니다.
- 이를 해결하기 위해서는 Block Size에 맞는 Buffer Cache를 다시 설정해야 합니다.

---

### Tablespace와 Datafile 매핑 조회

- 다음 명령어를 통해 Tablespace와 Datafile의 매핑 정보를 조회합니다.
```sql
  SELECT t.name tablespace_name, d.bytes, d.name file_name
  FROM v$tablespace t, v$datafile d
  WHERE t.ts# = d.ts#
  ORDER BY 1;
```

![62](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/62.png)

- Tablespace와 Datafile을 **내부 번호(ts#)** 기준으로 매핑하여 조회합니다.
- 각 Tablespace가 어떤 물리 파일을 사용하는지 정확하게 확인할 수 있습니다.
- DBA_DATA_FILES보다 더 **내부 구조 기반 조회**입니다.

---

### 16K Tablespace에서 테이블 생성 시 에러

![63](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/63.png)

- 16K Tablespace는 있지만 16K Buffer Cache가 없어 테이블 생성이 실패합니다.
- Tablespace만 존재한다고 해서 사용할 수 있는 것이 아닙니다.
- 해당 Block Size에 맞는 Buffer Cache가 반드시 필요합니다.
- DDL 작업(테이블 생성)도 Buffer Cache 영향을 받습니다.

---

### Tablespace 삭제 및 Datafile 정리

- 다음 명령어를 통해 Tablespace를 삭제합니다.
```
  DROP TABLESPACE imsy;
```

![64](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/64.png)

- 실무에서는 Buffer Cache를 먼저 없애지 않고, Tablespace를 먼저 삭제 해주어야 합니다.

---

### Datafile 삭제

- 먼저 Data File 여부를 확인해줍니다.
```sql
  !ls /app/ora19c/oradata/disk1/
```

- 다음 명령어를 통해 Data File을 삭제합니다.
```sql
  !rm /app/ora19c/oradata/disk1/imsy01.dbf
```

![65](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/65.png)

- Tablespace는 삭제 되었지만 Data File은 삭제되지 않았습니다.
  따라서 Data File을 OS에서 별도로 삭제해야 합니다.

---

### Data File 삭제 후 확인

![66](/KH_Security/Oracle/imgs/04_Tablespace%26Data%20File/66.png)

- 삭제한 Tablespace의 Datafile이 완전히 제거되었습니다.
- IMSY Tablespace와 관련된 Datafile이 더 이상 존재하지 않습니다.
- 현재 시스템에는 기본 Tablespace들만 남아있는 상태입니다.
- DBA_DATA_FILES 조회 결과 IMSY Tablespace와 관련된 Datafile이 더 이상 존재하지 않음을 확인하였으며,  
이를 통해 Tablespace 삭제와 Datafile 제거 작업이 정상적으로 완료되었음을 확인하였습니다.
