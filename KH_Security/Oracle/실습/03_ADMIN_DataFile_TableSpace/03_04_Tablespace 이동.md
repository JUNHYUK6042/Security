# Tablespace 이동

## 사용 명령어

#### **Tablespace OFFLINE**
```sql
  ALTER TABLESPACE <tablespace명> OFFLINE;
```  
- 지정한 Tablespace를 OFFLINE 상태로 변경합니다.
- 해당 Tablespace는 더 이상 접근할 수 없습니다.
- 데이터파일 이동, 복구, 유지보수 작업 시 사용합니다.

---

#### Datafile 경로 변경 (Rename)
```sql
  ALTER TABLESPACE <tablespace> RENAME DATAFILE
  <원래 data file명> TO <이동한 data file>;
```
- 데이터파일의 경로를 변경합니다.
- 실제 OS에서 파일을 이동한 후 실행해야 합니다.
- 디스크 변경, 경로 변경 시 사용합니다.
- 반드시 OS에서 파일 이동 후 실행해야 합니다.

---

#### Tablespace ONLINE
```sql
  ALTER TABLESPACE <tablespace> ONLINE;
```
- OFFLINE 상태의 Tablespace를 다시 활성화합니다.
- 정상적으로 데이터 접근이 가능합니다.

---

#### Datafile OFFLINE (DROP 옵션)
```sql
  ALTER DATABASE DATAFILE '<data file>' OFFLINE [DROP];
```
- 특정 데이터파일을 OFFLINE 상태로 변경합니다.
- DROP 옵션은 파일 손상 시 사용합니다.
- 데이터파일 손상 시 강제로 제외합니다.
- 복구 불가능한 상황에서만 사용해야 합니다.

---

## Tablespace 이동 (OPEN) 실습

- 다음 명령어를 통해 이동하고자 하는 Tablespace를 OFFLINE 상태로 변경합니다.
```sql
  ALTER TABLESPACE user OFFLINE;
```

- 다음 명령어로 Tablespace를 조회합니다.
```sql
  SELECT tablespace_name, status, contents, extent_management, segment_space_management
  FROM dba_tablespaces                                      
  ORDER BY 1; 
```

![29]()

- 다음 명령어로 Datafile를 조회합니다.
```sql
  SELECT tablespace_name, bytes, file_name FROM dba_data_files;
```

![30]()

- Offline된 Users Tablespace은 Data File의 크기가 표시되지 않습니다.
> System이나 Undo용 Tablespace는 Offline이 되지 않습니다.

---

![31]()

- Offline된 Users Tablespace은 Data File의 크기가 0으로 표시됩니다.

---

### Data File 이동

- 다음 명령어로 DB19 디렉터리에 있는 파일을 확인합니다.
```sql
  !ls /app/ora19c/oradata/DB19/
```

- 다음 명령어로 파일을 이동합니다.
```sql
  !mv /app/ora19c/oradata/DB19/users01.dbf /app/ora19c/oradata/disk3/
```

- 이동한 디렉터리를 확인합니다.
```sql
  !ls /app/ora19c/oradata/disk3/
```

![32]()

- 확인한 결과 DB19 디렉터리에 있는 users01.dbf 파일을 disk3 디렉터리로 이동하였습니다.

---

### Data File 조회

```sql
  SELECT tablespace_name, bytes, file_name
  FROM dba_data_files;
```

![33]()

- 확인한 결과로 Data file은 이동을 했지만, directory 정보는 수정되지 않습니다.

#### 수정되지 않는 이유
- OS와 Oracle은 서로 독립적으로 동작합니다.
- Datafile 경로는 Control File에 기록되므로,
  OS에서 파일을 이동해도 Control File 정보는 변경되지 않습니다.

---

### Directory 정보 수정

- 다음 명령어로 Control File의 정보를 수정합니다.
```
  ALTER TABLESPACE users RENAME DATAFILE
  '/app/ora19c/oradata/DB19/users01.dbf' TO '/app/ora19c/oradata/disk3/users01.dbf';
```

![34]()

- data file을 확인한 결과 users01.dbf 파일이 disk3 디렉터리로 이동하였습니다.

---

### TableSpace ONLINE

- 이동된 Tablespace를 ONLINE 상태로 변경합니다.
```sql
  ALTER TABLESPACE users ONLINE;
```

![35]()

- Datafile 경로 변경이 정상 적용되고 Tablespace가 정상 동작 상태로 복구되었습니다.
- Users Tablespace은 Data File의 크기가 다시 표시됩니다.

---

## Close 상태에서 Tablespace 이동

### Mount 상태에서 동작 과정

1. DB Shutdown
2. Data File 이동
3. DB Mount
4. ALTER DATABASE 명령으로 Data File 등록
5. DB OPEN

---

### 사용명령어

#### Datafile 경로 변경 (Rename)
```sql
  ALTER DATABASE RENAME FILE
  <원래 data file명> TO <이동한 data file명>;
```
- 데이터파일의 경로를 변경합니다.
- 실제 OS에서 파일을 이동한 후 실행해야 합니다.
- 디스크 변경, 경로 변경 시 사용합니다.
- 반드시 OS에서 파일 이동 후 실행해야 합니다.

---

## Tablespace 이동 (Close) 실습

### Data File 이동

- 먼저 Data file을 이동시키기 위해 다음 명령어로 DB를 Shutdown 합니다.
```sql
  SHUTDOWN IMMEDIATE
```

---

- 다음 명령어를 통해 Tablespace의 Datafile을 새로운 디렉터리로 이동합니다.
- 다음과 같은 파일들은 핵심 데이터 파일이므로 DB가 실행 중일 때 이동하면 안 됩니다.
```sql
  !mv /app/ora19c/oradata/DB19/sysaux01.dbf /app/ora19c/oradata/disk3/
  !mv /app/ora19c/oradata/DB19/system01.dbf /app/ora19c/oradata/disk3/
  !mv /app/ora19c/oradata/DB19/undotbs01.dbf /app/ora19c/oradata/disk3/
  !mv /app/ora19c/oradata/DB19/temp01.dbf  /app/ora19c/oradata/disk3/
```

- 이동 후 DB를 MOUNT 단계까지 Startup 합니다.
```sql
  STARTUP MOUNT
```

- Mount 단계는 Control File까지만 읽습니다. (Data File과 Redo Log File의 위치, 정보에 대한 내용이 있습니다.)

![36]()

- Mount 단계에서는 Data File을 읽지 못하기 때문에 파일 이동이 가능합니다.

---

### Directory 정보 확인

- 다음 명령어로 Tablespace와 Datafile 정보를 조회합니다..
```sql
  SELECT t.name tablespace_name, d.bytes, d.name file_name
```

![37]()

- MOUNT 상태에서 Datafile 정보를 조회한 결과 일부 파일은 기존 경로를 유지하고 있어  
  OS에서 이동된 경로와 불일치 상태이며, 이로 인해 Database OPEN 전에 반드시 Datafile 경로를 수정해야 합니다.

---

### Data File 경로 수정 명령어
```sql
  ALTER DATABASE RENAME FILE
  '/app/ora19c/oradata/DB19/sysaux01.dbf'
  TO '/app/ora19c/oradata/disk3/sysaux01.dbf';

  ALTER DATABASE RENAME FILE
  '/app/ora19c/oradata/DB19/system01.dbf'
  TO '/app/ora19c/oradata/disk3/system01.dbf';

  ALTER DATABASE RENAME FILE
  '/app/ora19c/oradata/DB19/undotbs01.dbf'
  TO '/app/ora19c/oradata/disk3/undotbs01.dbf';
```

- 경로 수정 후 Data File의 정보를 조회합니다

![38]()

---

### Control File & Redo Log File 확인

```sql
  SELECT name FROM v$controlfile;
```

![40]()

```sql
  SELECT member FROM v$logfile;
```

![41]()

- DB19 디렉터리에 있는 모든 파일들이 disk* 디렉리로 이동하였으므로 DB를 OPEN 상태로 변경합니다.
