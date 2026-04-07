# Oracle Instance 관리

## 개요

- Oracle은 Instance와 Database 구조로 동작한다.
- 사용자가 SQL문을 요청하면 서버 프로세스가 인스턴스(SGA)를 이용하여 데이터베이스에 접근하고 SQL을 실행한다.
- Instance는 메모리(SGA)와 백그라운드 프로세스로 구성된다.

---

## Oracle 시작과 종료

### 사용 명령어

- **STARTUP 명령어**

| 명령어 | 옵션 | 설명 | 예시 |
| ------ | ---- | ---- | ---- |
| STARTUP | `NOMOUNT` | Instance만 시작 (Parameter File 읽음) | STARTUP NOMOUNT; |
|         | `MOUNT` | Control File까지 읽음 | STARTUP MOUNT; |
|         | `OPEN` | Data File까지 열어 정상 사용 상태 | STARTUP; |
| STARTUP OPEN | `READ ONLY` | 읽기 전용으로 DB 오픈 | STARTUP OPEN READ ONLY; |
|              | `READ WRITE | 읽기 상태로 DB 오픈 | STARTUP OPEN READ WRITE; |

- **NOMOUNT** : Parameter File을 읽음으로써 컨트롤 파일에 관련된 정보를 제공합니다.
- **MOUNT** : Control File을 읽음으로써  
`데이터 파일`과 `리두 로그 파일`의 위치, 이름, 상태를 확인하는 정보를 제공합니다.
- **OPEN** : 데이터베이스를 활성화 하여, OPEN 상태에서 `데이터 파일`과 `리두 로그 파일`에 접근할 수 있는 상태입니다.  
또한 STARTUP 명령어를 옵션 없이 입력했을 때 기본 옵션으로 사용됩니다.

---

- **ALTER DABASE 명령어**
```
  ALTER DATABASE [ MOUNT | OPEN [ READ ONLY | READ WRITE ]];
```

- DB가 SHUTDOWN 상태가 아니라 NOMOUNT나 MOUNT 상태인 경우 오라클을 더 상위 단계로 올리기위해 사용합니다.
- NOMOUNT 상태의 DB를 한 번에 OPEN 상태로 올릴 수는 없습니다.
- `MOUNT` : MOUNT 단계로 DB를 변경합니다.
- `OPEN` : OPEN 단계로 DB을 변경합니다.
- `READ ONLY` : DB를 읽기전용으로 OPEN합니다.
- `READ WRITE` : DB를 읽기쓰기 상태로 OPEN합니다.

---

- **SHUTDOWN 명령어**

| 명령어 | 옵션 | 설명 | 예시 |
| -------- | ------ | ------ | ------ |
| SHUTDOWN | `NORMAL` | 사용자 종료까지 대기 후 종료 | SHUTDOWN NORMAL; |
|          | `TRANSACTIONAL` | 트랜잭션 완료 후 종료 | SHUTDOWN TRANSACTIONAL; |
|          | `IMMEDIATE` | 즉시 종료 (가장 많이 사용) | SHUTDOWN IMMEDIATE; |
|          | `ABORT` | 강제 종료 (비정상 종료) | SHUTDOWN ABORT; |

- **SHUTDOWN** 명령어 중에서 `IMMEDIATE` 명령어와 `ABORT` 명령어를 주로 사용합니다.
- **NORMAL** : 모든 사용자가 접속을 종료할 때까지 기다린 후에 종료합니다.
- **TRANSACTIONAL** : 진행 중인 트랜잭션이 끝날 때까지 기다린 후 종료합니다.
- **IMMEDIATE** : 진행 중인 작업을 모두 중단시키고 즉시 종료합니다. (트랜잭션 롤백이 있습니다.)
- **ABORT** 강제로 인스턴스를 종료 (비정상 종료)합니다. (트랜잭션 롤백이 없습니다.

---

## Oracle STARTUP & SHUTDOWN 명령어 실습

### 관리자 계정 접속
```
  sqlplus / as sysdba
```

---

### STARTUP (OPEN)
- 뒤에 옵션이 없을 때는 기본 옵션으로 OPEN 옵션을 사용합니다.

![01](/KH_Security/Oracle/imgs/01_오라클%20기초/01.png)

- Oracle Instance를 시작하여 데이터베이스를 사용할 수 있도록 합니다.

---

### DB 상태 확인
```sql
  SELECT STATUS FROM V$INSTANCE;
```
![DB](/KH_Security/Oracle/imgs/01_오라클%20기초/DB%20Status.png)

- 위의 결과처럼 DB를 오픈 단계까지 실행한 상태인 것을 알 수 있습니다.
- `v$instance` : 현재 오라클 인스턴스의 상태(STARTED, MOUNTED, OPEN 등)를 확인하는 뷰입니다.
- `v$` : 오라클 내부 동작 상태와 성능 정보를 실시간으로 제공하는 동적 성능 뷰(Dynamic Performance View)입니다.

---

### SHUTDOWN (IMMEDIATE)
- SHUTDOWN 명령어의 기본 옵션은 `NOMARL`입니다.
- 보통 `IMMEDIATE`와 `ABORT` 옵션을 사용합니다.

![SHUTDOWN](/KH_Security/Oracle/imgs/01_오라클%20기초/SHUTDOWN.png)

- 현재 진행 중인 작업을 즉시 중단하고 종료합니다.

---
