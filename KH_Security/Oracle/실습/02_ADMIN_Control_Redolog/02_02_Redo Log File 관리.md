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

![05](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/05.png)

- GROUP 3이 CURRENT 상태이므로 현재 Redo Log로 사용 중입니다.
- GROUP 1, 2는 INACTIVE 상태로 현재 사용되지 않고 있으며 필요 시 재사용 가능합니다.

--- 

## Archive Log 모드 및 로그 상태 확인
```sql
  archive log list
```

![06](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/06.png)

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

![07](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/07.png)

---

### Redo Log 그룹 삭제

- 다음 명령어를 통해 Redo Log File에서 있던 그룹을 삭제합니다.
```
  ALTER DATABASE DROP LOGFILE GROUP #;
```

![08](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/08.png)

- GROUP 3은 CURRENT 상태이므로 삭제를 할 수 없습니다.

---

- 다음 명령어를 통해 로그파일을 스위치합니다.
- 이후 CHECKPOINT를 통해 Redo Log File의 로그 상태를 확인합니다.
```sql
  ALTER SYSTEM SWITCH LOGFILE;
```

![09](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/09.png)

- Redo Log Switch 수행 이후 각 그룹의 상태 변화와 현재 사용 중인 로그를 확인하는 작업입니다.

![10](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/10.png)

-  CHECKPOINT 동작
  - 메모리(DB Buffer Cache)에 있는 변경된 데이터를 데이터파일로 기록합니다.
  - 해당 시점까지의 Redo Log 내용이 디스크에 반영됩니다.
  - 복구 시 필요한 범위를 줄여줍니다.

- `!vi switch.sql` 텍스트 편집기를 이용하여 스크립트로 작성한 뒤 Redo Log를 전환하고(Check Switch),  
Checkpoint를 수행하는 명령어를 반복 실행하여 로그 상태를 변경하는 작업입니다.
```sql  
  ALTER SYSTEM SWITCH LOGFILE;
  ALTER SYSTEM CHECKPOINT;
```

![11](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/11.png)

![12](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/12.png)

- switch 스크립트를 두번 실행시킨 결과 다음과 같이 Current 상태가 Group 3번으로 바뀌었습니다.

![13](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/13.png)

---

## Redo Log Group  삭제 및 물리 파일 제거

### Redo Log Group 삭제

- 다음 명령어를 통해 Group를 삭제합니다.
```sql
  ALTER DATABASE DROP LOGFILE GROUP 2;
```

![15](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/15.png)

- INACTIVE 상태의 Redo Log Group 2은 정상적으로 삭제된 것을 확인할 수 있습니다.

### 물리 파일 제거

- 다음 명령어를 통해 Group 2에 있던 물리적인 파일을 제거합니다.
```sql
  !rm /app/ora19c/oradata/DB19/redo02.log
```

![16](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/16.png)

---

## Redo Log Group 추가 및 상태 변화

### Redo Log 그룹 추가

- 다음 명령어를 통해 Group 생성과 redo04.log파일의 크기를 50MB로하며 생성합니다.
```sql
  ALTER DATABASE ADD LOGFILE GROUP 4
  '/app/ora19c/oradata/DB19/redo04.log' SIZE 50M;
```

![17](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/17.png)

### 추가 후 상태 확인

```sql
  SELECT a.group#, a.member, b.bytes, b.status, b.sequence#
  FROM v$logfile a, v$log b
  WHERE a.group# = b.group#
  ORDER BY 1;
```

#### Group 4

![18](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/18.png)

- 스크립트를 실행한 후 새로 생긴 Group 4의 상태가 UNUSED -> CURRENT로 전환되었습니다.
- Group 3은 CURRENT -> INACTIVE 상태로 전환되었습니다.


#### Group 5

- Group 4의 과정과 똑같이 진행한 결과로 UNUSED로 처음과 같은 상태로 다음과 같이 생성되었습니다.

![19](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/19.png)

- 스크립트를 한번 실행한 후 상태 확인 해줍니다.

![20](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/20.png)

---

## Redo Log Member 추가와 삭제

- 다음 실습을 통해 지정한 그룹에 멤버 파일을 추가 및 삭제합니다.
- 하나의 그룹에 여러 개의 멤버를 둘 수 있으면 이는 다중화를 의미합니다.

### Redo Log Member 추가 및 상태 확인

```sql
  ALTER DATABASE ADD LOGFILE MEMBER 
  '/app/ora19c/oradata/DB19/redo04_2.log' TO GROUP 4,
  '/app/ora19c/oradata/DB19/redo05_2.log' TO GROUP 5;
```

![21](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/21.png)

- `Database altered` 출력 됐으므로 정상적으로 멤버 파일이 추가된 것을 알 수 있습니다.

![22](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/22.png)

- 위의 결과 처럼 Group 4, Group 5에 각각 멤버파일이 추가 된 것을 알 수 있습니다.

### Redo Log Member 삭제 및 상태 확인
```sql
  ALTER DATABASE DROP LOGFILE MEMBER '/app/ora19c/oradata/DB19/redo04.log';
  ALTER DATABASE DROP LOGFILE MEMBER '/app/ora19c/oradata/DB19/redo01.log';
```

#### Redo Log Member 삭제

![23](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/23.png)

- 해당 그룹에서 멤버를 삭제하면 로그 파일 구성이 깨지기 때문에 발생한 오류입니다.
즉, 현재 그룹 구조에서 해당 멤버가 반드시 필요한 상태입니다.
- 로그 그룹이 정상 동작하려면 최소한의 유효한 구조를 유지해야 합니다.

![24](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/24.png)

- 그룹 1에 존재하는 마지막 멤버이기 때문에 삭제할 수 없습니다.
- Redo Log 그룹은 최소 1개의 멤버를 반드시 유지해야 하므로 삭제할 수 없습니다.

---

#### Redo Log Memeber 상태확인 후 삭제

![25](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/25.png)

- 멤버파일이 삭제가 안된 것을 확인할 수 있습니다.
- @switch 스크립트 파일을 실행시킨 후 다시 삭제를 해보았습니다.

![26](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/26.png)

- 위의 결과처럼 정상적으로 삭제가 된 것을 확인할 수 있습니다.

---

#### 삭제 후 상태 확인
```sql
  SELECT a.group#, a.member, b.bytes, b.status, b.sequence#
  FROM v$logfile a, v$log b
  WHERE a.group# = b.group#
  ORDER BY 1;
```

![27](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/27.png)

- 정상적으로 삭제가 된 것을 확인할 수 있습니다.
