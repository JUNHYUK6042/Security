# Control File & RedoLog File 관리

---

## 실습 Control Fiile 확인

- Control File은 데이터베이스의 구조 정보를 저장하는 핵심 파일입니다.
- Redo Log는 데이터 변경 이력을 기록하여 장애 발생 시 복구를 가능하게 합니다.
- 두 파일 모두 데이터베이스 운영에 필수적이며, 손상 시 복구가 어려워 다중화(미러링)가 필요합니다.

---

## Spfile 환경에서 control file 다중화

### 실습 순서
- 01. DB의 상태를 확인합니다.
- 02. control_files 파라미터를 수정합니다.
- 03. DB를 Shutdown 합니다.
- 04. 파라미터에 정의한 것과 같이 control file의 물리적 상태를 수정합니다.
- 05. DB를 Startup 합니다.

---

### 디렉터리 생성

- 실습을 하기 전에 '/app/ora19c/oradata/' 디렉터리 밑에  
disk1, disk2, disk3, disk4, disk5 디렉터리를 만들어줍니다.

![01](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/01.png)

---

### Control File 파라미터 값 변경

- 다음 명령어를 통해 컨트롤 파일 경로를 disk4, disk5로 변경하도록 설정합니다.
- 반드시 종료 하고 control file의 물리적 상태를 변경 후 db를 startup 해줍니다.
```sql
  ALTER SYSTEM SET control_files =
  '/app/ora19c/oradata/disk4/control.ctl',
  '/app/ora19c/oradata/disk5/control.ctl' scope = spfile;

  shutdown immediate
```

![02](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/02.png) 

#### 컨트롤 파일 복사 및 위치 확인

- `cp` 명령어를 통해 `/app/ora19c/oradata/DB19/`경로에 있는  
`control01.ctl`, `control02.ctl` 파일을 각각 `disk4`, `disk5` 디렉터리로 복사합니다.
- 이후 `ls` 명령어를 사용하여 각 디렉터리에 `control.ctl` 파일이 정상적으로 생성되었는지 확인합니다.

![03](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/03.png)

- 파일이 정상적으로 생성됐으므로 DB를 startup해줍니다.

---

- 다음 명령어를 통해 controlfile 경로가 잘 적용이 되었는지 확인합니다.
```sql
  SELECT name, value FROM v$parameter
  WHERE name LIKE 'control_files';
```
  
![04](/KH_Security/Oracle/imgs/03_Control%20%26%20RedoLog%20File/04.png)

- 조회 결과에 설정한 경로(`disk4`, `disk5`)에 control_files 파라미터가 정상적으로 적용된 것을 볼 수 있습니다.

---


