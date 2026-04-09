# Redo Log File 관리 실습

## 개요

- Redo Log는 데이터 변경 이력을 기록하여 장애 발생 시 복구를 가능하게 합니다.

---

## Redo Log File 확인

- Redo Log 파일의 그룹 정보, 파일 경로, 크기, 상태, 시퀀스를 조회하여 현재 로그 상태를 확인하는 작업입니다.

### 명령어
```sql
  SELECT a.group#, a.member, b.bytes, b.status, b.sequence#
  FROM v$logfile a, v$log b
  WHERE a.group# = b.group#
  ORDER BY 1;
```

![05]()

- GROUP 3이 CURRENT 상태이므로 현재 Redo Log로 사용 중입니다.
- GROUP 1, 2는 INACTIVE 상태로 현재 사용되지 않고 있으며 필요 시 재사용 가능합니다.

--- 

## Archive Log 모드 및 로그 상태 확인
```sql
  archive log list
```

![06]()

- 데이터베이스의 아카이브 모드 설정 여부와 Redo Log 보관 상태를 확인하는 명령입니다.
- Database log mode가 No Archive Mode일 경우, 아카이브 로그는 생성되지 않지만 출력되는  
sequence 정보는 현재 Redo Log 상태를 보여주는 값이므로 유효한 정보입니다.

---

## Log Switch & CheckPoint

### 명령어
```sql
  ALTER SYSTEM SWITCH LOGFILE;
    - Log switch를 강제로 발생 시킨다.
  ALTER SYSTEM CHECKPOINT;
    - Check point를 강제로 발생 시킨다.
```

- Redo Log File의 로그 상태를 확인합니다.

![07]()

---

### Redo Log 그룹 삭제

- 다음 명령어를 통해 Redo Log File에서 있던 그룹을 삭제합니다.
```
  ALTER DATABASE DROP LOGFILE GROUP #;
```

![08]()

- GROUP 3은 CURRENT 상태이므로 삭제를 할 수 없습니다.

---

- 다음 명령어를 통해 로그파일을 스위치합니다.
- 이후 CHECKPOINT를 통해 Redo Log File의 로그 상태를 확인합니다.
```sql
  ALTER SYSTEM SWITCH LOGFILE;
```

![09]()

- Redo Log Switch 수행 이후 각 그룹의 상태 변화와 현재 사용 중인 로그를 확인하는 작업입니다.

![10]()

-  CHECKPOINT 동작
  - 메모리(DB Buffer Cache)에 있는 변경된 데이터를 데이터파일로 기록합니다.
  - 해당 시점까지의 Redo Log 내용이 디스크에 반영됩니다.
  - 복구 시 필요한 범위를 줄여줍니다.

---

###
