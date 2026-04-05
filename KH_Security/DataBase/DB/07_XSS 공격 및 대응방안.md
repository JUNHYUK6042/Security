# XSS 공격 및 대응방안

## 개요

- XSS란 웹사이트에 악의적인 스크립트를 삽입하여 사용자의 브라우저에서 실행되도록 하는 공격입니다.
- 이를 통해 세션 탈취, 사용자 정보 탈취, 강제 리다이렉션 등의 공격이 가능합니다.

---

## 취약한 코드

- 다음과 같이 사용자 입력을 그대로 DB에 저장하는 경우 문제가 발생합니다.

```sql
  $sql="INSERT INTO board (bno, subject, bdate, hit, id)
  VALUES (board_bno_seq.nextval,'$subject', sysdate, 0, '$id')";
```
```sql
  $sql="INSERT INTO content (bno, content)
  VALUES (board_bno_seq.currval, '$content')";
```
- 사용자의 입력값이 필터링 없이 저장됩니다.
- 이후 출력될 때 스크립트로 실행될 수 있습니다.

---

## XSS 공격 실습

### 내용
```
  <script>alert("XSS 공격 실습")</script>
```

![13](/KH_Security/DataBase/img/13.png)

### 결과

- 게시물 클릭시 스크립트 파일이 실행됩니다.
이러한 스크립트 공격을 통해 세션 쿠키, 관리자 계정 탈취가 가능하게 됩니다.
- 또한 사용자 강제 리다이렉션도 가능합니다.
- 브라우저에서 실행되기 때문에 탐지가 어렵습니다.

![14](/KH_Security/DataBase/img/14.png)

---

## 대응방안

- XSS 공격을 방지하기 위해 다음과 같은 코드를 넣어주어야 합니다.

```php
  $subject = strip_tags(htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'));
  $content = strip_tags(htmlspecialchars($content, ENT_QUOTES, 'UTF-8'));
```

- `htmlspecialchars()`를 통해 특수문자를 HTML 엔티티로 변환하여 브라우저에서 태그로 해석되지 않도록 하며,  
`strip_tags()`는 추가적으로 불필요한 HTML 태그를 제거하는 용도로 사용됩니다.

### 결과

![15](/KH_Security/DataBase/img/15.png)

![16](/KH_Security/DataBase/img/16.png)

- 위의 결과처럼 공격이 먹히지 않고 그냥 문자열로 나오는 것을 확인할 수 있습니다.
