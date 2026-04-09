# Parameter File 관리

## 개요

- 파라미터 파일은 Oracle 인스턴스를 시작하기 위한 설정값을 저장하는 파일입니다.
- 인스턴스의 메모리 크기, 파일 위치, 동작 방식 등의 정보를 관리합니다.
- 파라미터 파일에는 PFILE과 SPFILE 두 종류가 있습니다.
- PFILE은 text파일이며, SPFILE은 바이너리 파일입니다.

---

## Parameter File 확인

- `sqlplus / as sysdba` 명령어로 관리자 세션으로 접속합니다.

```sql
  SELECT name, value FROM v$parameter
  WHERE name = 'spfile';
```

![01](/KH_Security/Oracle/imgs/02_Parameter%20File/01.png)

- 파일의 종류는 spfile인 것알 알 수 있습니다.
- value 값은 spfile의 경로를 확인합니다.
- spfile은 직접 수정이 불가능 하며, 명령어로만 수정이 가능합니다.

---

- 다음 명령어를 통해 인스턴스의 타입과 인스턴스의 이름을 확인합니다.
```sql
  SHOW PARAMETER instance_name
```
![02](/KH_Security/Oracle/imgs/02_Parameter%20File/02.png)

- `instance_name` 값이 DB19로 설정되어 있으며, 현재 실행 중인 오라클 인스턴스의 이름을 의미합니다.

---

- `$ORACLE_HOME/dbs` 경로에서 파라미터 파일(PFILE, SPFILE)과 비밀번호 파일이 존재하는 것을 확인할 수 있습니다.
- `ls` 명령어는 리눅스 명령어이기 때문에 sql에서 사용하기 위해서는 앞에 `!`표를 붙여줘야 합니다. 
```sql
  !ls $ORACLE_HOME/dbs
```

![03](/KH_Security/Oracle/imgs/02_Parameter%20File/03.png)

- `$ORACLE_HOME/dbs` 디렉터리에서 SPFILE(spfileDB19.ora), 비밀번호 파일(orapwDB19),  
기타 설정 파일(init.ora 등)이 존재하는 것을 확인할 수 있습니다.

---

## SPFILE 환경 (Parameter 확인 & 수정)

### 명령어

```sql
  ALTER SYSTEM SET <parameter 명> = <값>;
    - 지정한 파라미터의 값을 수정합니다.
    - spfile 사용 환경에서 설정된 파라미터 값은 항구적으로 수정됩니다.
    - 동적인 파라미터만 수정 가능합니다.
  
  SELECT NAME, VALUE FROM V$PARAMETER;
    - 현재 운영중인 parameter 값을 조회합니다.
    - 'SHOW PARAMETER ~'에 출력 값과 동일합니다.

  SELECT NAME, VALUE FROM V$SPPARAMETER;
    - spfile에 설정된 값을 조회합니다.
```

---

### Parameter 값 출력

- `name`에 undo가 들어가는 파라미터 값을 출력합니다.
```sql
  SELECT name, value from v$spparameter
  WHERE name LIKE = '%undo%';
```

![04](/KH_Security/Oracle/imgs/02_Parameter%20File/04.png)

- v$spparameter는 SPFILE(파라미터 파일)에 저장된 값을 의미합니다.
- 설정 파일에 저장된 값을 확인하며, 명시적으로 설정된 값만 출력합니다.

#### 결과 해석

- undo_tablespace = UNDOTBS1
  - UNDO 데이터가 저장되는 테이블스페이스

---

- v$parameter는 현재 메모리(SGA)에 적용된 값을 의미한다. 
- 현재 DB에서 실제 적용 중인 값 확인합니다.
- UNDO는 트랜잭션 롤백과 읽기 일관성을 위해 사용됩니다.

```sql
  SELECT name, value from v$parameter
  WHERE name LIKE = '%undo%';

  SHOW PARAMETER undo;  
```

![05](/KH_Security/Oracle/imgs/02_Parameter%20File/05.png)  
![06](/KH_Security/Oracle/imgs/02_Parameter%20File/06.png)


#### 결과 해석

- temp_undo_enabled = FALSE
  - 임시 테이블에 대한 UNDO 사용 안 함

- undo_management = AUTO
  - UNDO를 자동으로 관리 (AUM 방식)

- undo_tablespace = UNDOTBS1
  - UNDO 데이터가 저장되는 테이블스페이스

- undo_retention = 900
  - UNDO 데이터를 900초(15분) 동안 유지

---

- 다음 명령어를 통해 파라미터 값 수정해줍니다.
```sql
  ALTER SYSTEM SET undo_retention=300;
```

![07](/KH_Security/Oracle/imgs/02_Parameter%20File/07.png)

---

- 다음 명령어를 통해 변경된 파라미터 값을 확인해줍니다.
```sql
  SELECT name, value FROM v$spparameter
  WHERE name = 'undo_retention';
```

![08](/KH_Security/Oracle/imgs/02_Parameter%20File/08.png)

- undo_retention의 파라미터 값이 300으로 설정되었습니다.
- 하지만 SCOPE 절 설정을 spfile로 했으므로, 지금은 파일에만 저장이 됩니다.
따라서, 메모리의 값이 300으로 바뀌려면 재시작 해주어야합니다.

---

- shutdown immediate 명령어와 startup 명령어를 통해 재접속한 뒤 파라미터 값을 확인합니다.
- 다음 명령어를 통해 현재 메모리에 적용된 파라미터 값을 확인합니다.
```
  SHOW PARAMETER undo_retention;
```

![09](/KH_Security/Oracle/imgs/02_Parameter%20File/09.png)

- 재시작 과정에서 SPFILE에 저장된 값이 메모리에 반영되므로, 변경된 파라미터 값이 적용된 것을 확인할 수 있습니다.

---

### SCOPE절 memory

- undo_retention 값을 변경하여 메모리와 SPFILE 값의 차이를 확인합니다.
- v$parameter는 현재 메모리 값, v$spparameter는 SPFILE 저장 값을 의미합니다.

![10](/KH_Security/Oracle/imgs/02_Parameter%20File/10.png)

```sql
  SHOW PARAMETER undo_retention; \\ 초기 상태 확인
  결과 : undo_retention = 300

  ALTER SYSTEM SET undo_retention = 600 SCOPE = memory; \\ 파라미터 변경(메모리 값만 변경)

  SHOW PARAMETER undo_retention; \\ 변경 후 상태 확인
  결과 : undo_retention = 600

  SELECT name, value FROM v$parameter \\ 현재 메모리값 확인
  WHERE name = 'undo_retention';
  결과 : undo_retention = 600

  SELECT name, value FROM v$spparameter \\ SPFILE에 저장된 값 확인
  WHERE name = 'undo_retention';
  결과 : undo_retention = 300
```

- 동작 흐름
  - 메모리 값만 변경하고, SPFILE 저장된 값은 변경되지 않습니다.
  - 재시작하면 원래 값(300)으로 복구됩니다.
 
---

### SCOPE절 spfile

- shutdown immediate 명령어와 
- undo_retention 값을 변경하여 메모리와 SPFILE 값의 차이를 확인합니다.
- v$parameter는 현재 메모리 값, v$spparameter는 SPFILE 저장 값을 의미합니다.

![11](/KH_Security/Oracle/imgs/02_Parameter%20File/11.png)

```sql
  SHOW PARAMETER undo \\ 초기 상태 확인
  결과 : undo_retention = 300

  ALTER SYSTEM SET undo_retention = 500 SCOPE = spfile; \\ 파라미터 변경(spfile에 저장된 값만 변경)

  SHOW PARAMETER undo_retention; \\ 변경 후 상태 확인

  SELECT name,value FROM v$parameter \\ 현재 메모리값 확인
  WHERE name = 'undo_retention';

  SELECT name,value FROM v$spparameter \\ SPFILE에 저장된 값 확인
  WHERE name = 'undo_retention';
```

- 동작흐름
  - scope절의 설정된 값이 spfile이므로, spfile에 저장된 값만 바뀌고, 메모리에 저장된 값은 바뀌지 않습니다.
  - 메모리값은 현재는 바뀌지 않고, 재시작 후에 변경된 값으로 적용됩니다.
 
