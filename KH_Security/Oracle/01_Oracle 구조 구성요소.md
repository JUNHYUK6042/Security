# Oracle 구조 구성요소

- Oracle Database는 Instance와 Database로 구성되어 있습니다.  
- Client는 User Process까지 존재하며, User Process가 Access하는 대상은 Server Process입니다.    
- Server Process는 매우 많이 생성되며, Client Process 수만큼 생성됩니다.  
- Server Process는 Memory에만 접근합니다.
- Server process는 **Database가 아니라 Instance에 붙으며,** 실제 데이터 접근은 Instance가 담당합니다.

---

## 구조
```
Instance = SGA + Background Process
Database = Datafile + Controlfile + Redo Log
```

[01]()

---

### SGA 구성 (Memory)

#### 내부구조
```
  SGA
  ├── Database Buffer Cache
  ├── Shared Pool
  └── Redo Log Buffer
```

---

#### Database Buffer Cache

- 데이터파일에서 읽은 데이터를 저장합니다.
- 실제 데이터 변경도 여기서 발생합니다.

- 예시
  - INSERT 수행 → 바로 디스크 저장하지 않습니다.
  - Buffer Cache에서 먼저 변경됩니다.

- 핵심
  - **모든 데이터 변경(트랜잭션)은 Buffer Cache에서 먼저 일어납니다.**
  - 데이터 변화를 시키면 데이터 버퍼 캐쉬에서 수정을 하고, Redo Log Buffer의 Redo Log 정보도 발생하게 됩니다.
 
---

#### Shared Pool
- SQL 문 & 실행계획을 저장합니다.

- 동작 과정
  - SQL 문장 검사합니다. (문법 체크)
  - 실행 계획을 생성합니다.
  - Library Cache에 저장합니다.

- 예시
  - 같은 SQL을 반복 실행하면 다시 분석하지 않고 재사용합니다.

- 정리  
  - **Shared Pool은 SQL과 실행 계획을 저장하고 재사용합니다.**
 
---

#### Redo Log Buffer
- 데이터 변경 이력을 기록합니다.
- 작업 일지를 작성한다고 생각하면 됩니다.

- 특징  
  - 시간 순서대로 기록합니다.  
  - 장애 복구에 사용됩니다.

- 정리
  - **Redo Log Buffer는 데이터 변경 내용을 기록합니다.**
  - Commit 시 Redo Log File에 작업 일지가 바로 기록됩니다.
 
---

### Backgroud Process

- Oracle은 여러 백그라운드 프로세스를 통해 자동으로 동작합니다.
- Background Process는 5개의 프로세스로 구성되어있습니다.
```
- PMON
- SMON
- DBWR
- LGWR
- CKPT
```

---

#### DBWR (Database Writer)
- DBWR는 Database Buffer Cache에 있는 데이터를 Data File로 내려쓰는 역할을 합니다.  
- Insert 시 데이터는 먼저 메모리(Instance)에서 처리됩니다.  
- Commit이 발생해도 데이터가 바로 디스크에 기록되는 것은 아닙니다.  
- 실제 디스크 기록은 DBWR가 수행합니다.
- 즉, 트랜잭션이 발생하면 메모리에 먼저 반영되고 이후 DBWR가 디스크로 반영합니다.  

---

#### LGWR (Log Writer)
- LGWR는 Redo Log Buffer의 내용을 Redo Log File로 기록합니다.  
- Commit 시 반드시 LGWR가 수행되어야 하며, LGWR가 기록해야 Commit이 완료됩니다.  

---

#### PMON (Process Monitor)
- SQLPlus 종료 후에도 Server Process가 메모리에 남을 수 있습니다.  
- PMON은 이러한 좀비 프로세스를 탐색하여 제거하는 역할을 합니다.  

---

#### SMON (System Monitor)
- 트랜잭션을 Commit한 이후 시스템 장애가 발생하면 Data File에는 데이터가 반영되지 않을 수 있습니다.  
- 하지만 Redo Log에는 해당 작업 내용이 기록되어 있습니다.  
- SMON은 Redo Log를 기반으로 Data File을 복구합니다. 이를 Instance Recovery라고 합니다.
  
---

#### CKPT (Checkpoint)
- Data File과 Redo Log File은 기록 시점이 서로 다릅니다.  
- 이로 인해 데이터베이스는 불안정한 상태가 될 수 있습니다.  
- Checkpoint는 이러한 상태를 해결하기 위해 동기화 이벤트를 발생시킵니다.  
- 트랜잭션 번호 기준으로 동기화를 수행합니다.  

---
