# 2차 배열을 위용한 DataBase 출력

---

## 데이터 흐름구조
```
  Oracle DB → SQL 실행 → 결과 배열($row) → 반복문 → HTML 테이블 출력
```

---

## 특징
- 한 번의 실행으로 모든 데이터를 가져옵니다.
- 반복문으로 데이터를 한 줄씩 출력합니다.
- `$row['컬럼명'][index]` 형태의 컬럼 기반 배열 구조를 사용합니다.
- DB 작업 후 바로 연결을 종료하여 자원을 효율적으로 사용합니다.

---

## 동작 코드

```php
  <?php
    echo("<a href=./st_in.html>자료 입력하기</a><hr>");
    require('conn.php'); // DB 연결 파일을 불러옴 (여기서 $conn 생성됨)
    
    $sql = "select sno, sname, sex, major, syear, to_char(avr,'0.00') avr
            from student
            order by sno";
    
    $result = oci_parse($conn, $sql); // SQL문을 Oracle에서 실행할 수 있도록 준비 (파싱 단계)
    oci_execute($result); // 파싱된 SQL문을 실제로 실행
    
    $row_num = oci_fetch_all($result, $row); // 실행 결과를 전부 가져와 배열 $row에 저장
                                             // 반환값은 전체 행(row)의 개수

    oci_free_statement($result); // 사용한 SQL 리소스를 메모리에서 해제 (자원 관리)
    oci_close($conn); // DB 연결 종료
    
    echo "<br>";
    echo("Row의 개수는 $row_num 입니다.<br><hr>");
    echo("<table border='0'>");
    
    for ($i=0; $i<$row_num; $i++) {
        echo("
              <tr>
                 <td width='50'><p align='center'>{$row['SNO'][$i]}</p></td> 
                 <td width='80'><p align='center'>{$row['SNAME'][$i]}</p></td>
                 <td width='20'><p align='center'>{$row['SEX'][$i]}</p></td>
                 <td width='20'><p align='center'>{$row['SYEAR'][$i]}</p></td>
                 <td width='50'><p align='center'>{$row['MAJOR'][$i]}</p></td> 
                 <td width='30'><p align='center'>{$row['AVR'][$i]}</p></td>
              </tr>
        ");
    }
    
    echo("</table>");
  
    echo "<hr><br>";
    show_source(__FILE__);
  ?>
```

---

## 결과

![05](/KH_Security/DataBase/img/05.png)
