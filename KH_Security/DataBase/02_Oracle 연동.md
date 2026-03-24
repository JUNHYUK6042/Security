# PHP와 오라클 연동

---

## 함수 정리

### oci_parse()
```
  resource oci_parse ( resource $connection , string $sql_text )
```
- SQL 문장을 Oracle이 실행할 수 있도록 “준비(파싱)”하는 함수입니다.
- 오라클 접속 식별자를 반환해줍니다.
- 파싱이란 해당 SQL 질의문을 컴퓨터가 실행 가능하도록 변환하는 과정입니다.
- C언어에서 컴파일하는 과정과 유사합니다.

| 매개변수 | 의미 |
| ------- | ----- |
| $connection | 네트워크 접속 식별자 |
| $sql_text | 파싱할 SQL문 |

---

### oci_bind_by_name()
```
  bool oci_bind_by_name ( resource $statement , string bv_name , mixed &$var)
```
- 파싱된 SQL문의 바인드 변수에 값을 제공합니다.
- 실행 여부를 불린 타입 값으로 반환합니다.
- SQL에 값을 직접 넣지 않고, 따로 안전하게 꽂아 넣는 방식입니다.

| 매개변수 | 의미 |
| ------- | ----- |
| $statment | oci_parse() 함수에 의해 반환된 오라클 접속 식별자 |
| bv_namet | 바이너리 변수 |
| &$var | 바이너리 변수에 값을 전달할 변수 |

---

### oci_execute()
```
  bool oci_execute ( resource $statement [, int $mode = OCI_COMMIT_ON_SUCCESS ] )
```
- SQL문을 실행합니다.
- 함수의 실행 결과는 불린 값으로 반환되지만 SQL문의 실행 결과는 $statement에 반환합니다.

| 매개변수 | 의미 |
| ------- | ----- |
| $statment | oci_parse() 함수에 의해 반환된 오라클 접속 식별자 실행 결과값이 저장됩니다. |
| $mode | DML 문일 경우 커밋방식을 정의 |
|  | OCI_COMMIT_ON_SUCCESS : 자동 커밋 |
|  | OCI_DEFAULT : 수동 커밋(커밋 함수 필요) |

---

### oci_fetch_array()
```
  array oci_fetch_array ( resource $statement [, int $mode ] )
```
- oci_execute에서 반환된 결과를 일차원 배열에 패치합니다.
- 행이 저장된 일차원 배열을 반환합니다.

| 매개변수 | 의미 |
| ------- | ----- |
| $statment | oci_parse() 함수에 의해 반환된 오라클 접속 식별자 실행 결과값이 저장됩니다. |
| $mode | 반환될 배열의 형식을 정의 |
|  | OCI_BOTH : 스칼라 배열과 연관 배열을 모두 생성 |
|  | OCI_ASSOC : 연관 배열만 생성 |
|  | OCI_NUM : 스칼라 배열만 생성 |
