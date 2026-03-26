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
| `$connection` | 네트워크 접속 식별자 |
| `$sql_text` | 파싱할 SQL문 |

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
| `$statment` | oci_parse() 함수에 의해 반환된 오라클 접속 식별자 |
| `bv_namet` | 바이너리 변수 |
| `&$var` | 바이너리 변수에 값을 전달할 변수 |

---

### oci_execute()
```
  bool oci_execute ( resource $statement [, int $mode = OCI_COMMIT_ON_SUCCESS ] )
```
- SQL문을 실행합니다.
- 함수의 실행 결과는 불린 값으로 반환되지만 SQL문의 실행 결과는 $statement에 반환합니다.

| 매개변수 | 의미 |
| ------- | ----- |
| `$statment` | oci_parse() 함수에 의해 반환된 오라클 접속 식별자 실행 결과값이 저장됩니다. |
| `$mode` | DML 문일 경우 커밋방식을 정의 |
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
| `$statment` | oci_parse() 함수에 의해 반환된 오라클 접속 식별자 실행 결과값이 저장됩니다. |
| `$mode` | 반환될 배열의 형식을 정의 |
|  | OCI_BOTH : 스칼라 배열과 연관 배열을 모두 생성 |
|  | OCI_ASSOC : 연관 배열만 생성 |
|  | OCI_NUM : 스칼라 배열만 생성 |

---

### oci_fetch_all()
```
  int oci_fetch_all ( resource $statement , array &$output)
```
- oci_execute에서 반환된 결과를 이차원 배열에 패치합니다.
- 패치된 행의 개수를 반환합니다.

| 매개변수 | 의미 |
| -------- | ---- |
| `$statment` | oci_execute() 함수에 의해 실행된 결과 값을 전달합니다 |
| `$mode` | 리소스가 패치될 2차원 배열 |

---

### empty()
```
  bool empty ( mixed $var )
```
-  $var 변수가 NULL인가 확인합니다.

| 매개변수 | 의미 |
| -------- | ---- |
| `$var` | NULL 값 여부를 판단할 매개 변수 |

---

### oci_close()
- 데이터 베이스 연결을 종료합니다.

---

### 예시

```php
<?
 echo("<a href=./st_in.html>자료 입력하기</a><hr>");
 require('conn.php');
 $sql="select sno,sname,sex,major,syear,avr // sql 질의 문
 from student order by sno";

 $result=oci_parse($conn,$sql); // 파싱
 oci_execute($result); // sql 질의 실제 실행 (자동 커밋)

 while ($row = oci_fetch_array($result,OCI_NUM)){ // $row = 한 행의 데이터가 담긴 배열 변수
 echo("$row[0]-$row[1]-$row[2]-$row[3]-$row[4]-$row[5] <br>"); // 각 배열의 요소 출력
 }
 oci_free_statement($result); // 사용이 끝난 쿼리 결과 자원을 해제하여 메모리를 시스템에 반환하고 더 이상 사용할 수 없게 함
 oci_close($conn); // db 연결 종료
?>
```

---

## Oracle 연동 실습

```php
  <?
    echo("<a href=./st_in.html>자료 입력하기</a><hr>");
    require('conn.php');
  
    $sql="select sno,sname,sex,major,syear,to_char(avr,'0.00') avr 
          from student order by sno";
  
    $result=oci_parse($conn,$sql); 
    oci_execute($result);                 
    
    echo("<table border='0'>"); 
    while ($row = oci_fetch_array($result, OCI_NUM)){  
           echo("
                 <tr> 
                    <td width='50'><p align='center'>{$row[0]}</p></td> 
                    <td width='80'><p align='center'>{$row[1]}</p></td>
                    <td width='20'><p align='center'>{$row[2]}</p></td>
                    <td width='50'><p align='center'>{$row[3]}</p></td>
                    <td width='50'><p align='center'>{$row[4]}</p></td> 
                    <td width='30'><p align='center'>{$row[5]}</p></td>
                 </tr>
                ");
          }
    echo("</table>");
  
    oci_free_statement($result);  
    oci_close($conn);
  
    echo "<hr><br>";
    show_source(__FILE__);
  ?>
```

- 다음과 같이 화면이 나온 것을 알 수 있습니다.

![04](/KH_Security/DataBase/img/04.png)
