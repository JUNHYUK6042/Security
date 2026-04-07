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

![01]()

- 파일의 종류는 spfile인 것알 알 수 있습니다.
- value 값은 spfile의 경로를 확인합니다.
- spfile은 직접 수정이 불가능 하며, 명령어로만 수정이 가능합니다.

---

- 다음 명령어를 통해 인스턴스의 이름과 타입 

```sql
  SHOW PARAMETER instance_name
```
![02]

---
