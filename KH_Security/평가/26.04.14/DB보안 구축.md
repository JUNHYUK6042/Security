# 평가

---

## 문제 1. 인스턴스와 데이터베이스의 구조 제어

### 문제 1-1. 데이터베이스를 nomount, mount, open 단계로 한단계씩 시작하는 과정의 명령을 기술하세요.

```text
startup nomount, alter mount, alter open
```

### 문제 	1-2. 1-1에서 각 단계별로 데이터베이스를 시작할때 각 단계가 어떤 단계인지 확인하는 명령을 기술하세요  

```text
select status from v$instance;
```

### 문제 1-3. 1G 크기의 st tablespace를 생성하는 명령을 기술하세요

```text
create tablespace st datafile '/app/ora19c/oradata/st01.dbf' size 1G;
```

---

## 문제 2. Security Domain을 구현하고 확인

### 문제 2-1. table01이 어느 유저의 소유인지 확인하는 명령을 작성하세요
```text
select owner from dba_tables where table_name = 'TABLE01';
```

### 문제 2-2. st01 유저에게 table01에 select 가능하도록 권한을 할당하세요.
```text
grant select on st_master.table01 to st01;
```

### 문제 2-3. table01 테이블의 구조를 수정할 수 있는 권한을 가진 ta_al role을 생성하는 명령을 작성하세요.
```text
create role ta_al;

grant alter on st_master.table01 to ta_al;
```

### 문제 2-4. ta_al role을 st01에 할당하는 명령을 작성하세요.
```text
grant ta_al to st01;
```

### 문제 2-5. table01 테이블에 트랜젝션을 수행 할 수 있는 권한을 가진 ta_ta role을 생성하는 명령을 작성하세요.
```text
create role ta_ta;

grant insert, update, delete on st_master.table01 to ta_ta;
```

---

## 문제 3. 보안성 강화 응용프로그램 구현

### 문제 3-1. sqlplus / as sysdba' 명령에서 sysdba에 대해서 설명하세요.
```text
sysdba는 관리자 전용의 권한으로 다른 사용자에게 grant 가능하지만 sys만 사용 가능하다.
```

### 문제 3-2. 오라클이 사용하는 보안 모델의 특징에 대해서 기술하세요
```text
비임의적 접근제어 모델인 take-grant 보안 모델은 오브젝트에 대한 접근 권한을 사용자가 다른 사용자에게 할당하는 것 뿐아니라
권한을 할당하는 권한까지 위임 가능한 모델로 변화하는 업무 환경에 적절히 대은 가능한 보안 모델이다.
```
