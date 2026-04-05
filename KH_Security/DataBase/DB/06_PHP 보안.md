# PHP.ini 보안 설정

## 개요
- PHP는 설정과 함수 사용에 따라 보안 수준이 크게 달라집니다.
- 주요 목적은 정보 노출 방지, 세션 보호, 외부 공격 차단입니다.

---

## PHP.ini 주요 보안

- `expose_php = Off` : PHP 버전 노출 방지
- `display_errors = Off` : 에러 메시지로 인한 정보 유출 방지
- `log_errors = On` : 에러는 파일로 기록
- `session.cookie_httponly = 1` : JS에서 쿠키 접근 차단 (XSS 방지)
- `session.cookie_secure = 1` : HTTPS에서만 쿠키 전송
- `session.use_strict_mode = 1` : 세션 고정 공격 방지
- `session.use_only_cookies = 1` : URL로 세션 ID 노출 방지
- `session.cookie_samesite = Lax/Strict` : CSRF 방지
- `allow_url_include = Off` : 원격 파일 포함 공격 방지
- `disable_functions = exec,passthru,shell_exec,system,proc_open,popen` : exec 등 위험 함수 차단
- `open_basedir = /home/httpd/html` : 접근 가능한 디렉토리 제한

---

## 슈퍼 글로버 배열 & Server 배열

### 슈퍼 글로버

- `$_GET` :URL 파라미터
- `$_POST` : 폼 데이터
- `$_REQUEST` : GET + POST + COOKIE (혼합, 위험)
- `$_SERVER` : 서버/요청 정보
- `$_SESSION` : 서버 저장 사용자 정보
- `$_COOKIE` : 클라이언트 저장 데이터

---

### Server

- `REMOTE_ADDR` : 사용자 IP
- `HTTP_HOST` : 도메인
- `REQUEST_METHOD` : 요청 방식 (GET/POST)
- `REQUEST_URI` : 요청 URL
- `HTTP_USER_AGENT` : 브라우저 정보
- `DOCUMENT_ROOT` : 웹 루트 경로

---

## XSS 공격 방지 

- `htmlspecialchars() :`
  - 특수문자를 HTML 엔티티로 변환
  - 스크립트 실행 차단 (출력 시 사용)
 
| 문자 | HTML 엔티티 |
| --- | --- |
| `<` | `&lt;` |
| `>` | `&gt;` |
| `"` | `&quot;` |
| `'` | `&#039;` |
| `&` | `&amp;` |

### 예시

```php
  $name = $_POST['name'];                                           // 위험
  $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');    // 안전
```

---

### 특징

| 함수 | 기능 | 용도 |
| --- | --- | --- |
| `strip_tags() | 태그 자체 제거 | 사용자 입력 정리 |
| `htmlspecialchars()  | 태그를 엔티티로 변환 (&lt; 등) | 출력 시 XSS 방지 |

---
