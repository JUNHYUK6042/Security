# 평가

---

## 문제 1. 응용프로그램 관리 능력

### 문제 1-1. PHP 7.4.33 컴파일 과정에 오라클 연동을 위한 옵션을 기술하세요.

```text
--with-oci8=instantclient,/usr/lib/oracle/19.25/client64/lib
```

### 문제 1-2. 오라클 서버 접속에 사용되는 TNS_NAME을 사용하기 위해   
### /app/appche/bin/apachectl에 반드시 설정해야 하는 환경변수 설정을 기술하세요

```text
export TNS_ADMIN=/usr/lib/oracle/network/admin​
```

### 문제 1-3. 설치후 기본 web page를 /home/httpd/html로 변경하기 위해 설정 
### /app/apache/conf/httpd.conf 파일에 설정해야 하는 것은 무엇인지 기술하세요

```text
DocumentRoot "/home/httpd/html" 경로로 수정해야합니다.
```

---

## 문제 2. 응용프로그램 실행 제어 기술

### 문제 2-1. 설치된 apache 서버를 재시작 하시오.
```text
/app/apache/bin/apachectl restart
```

---

## 문제 3. 보안성 강화 응용프로그램 구현

### 문제 3-1. Document Root 디렉토리를 /home/httpd/html로 수정한 보안상의 이유를 간단히 기술하세요
```text
apache 서버를 관리하는 시스템 관리자와 Web page를 관리자는
webmaster의 직무가 보안상 겸업할수 없는 직무이므로 직무분리를 위해 Document Root의 위치를 이동합니다.
```

### Document Root 디렉토리 접근에 403 Forbidden 에러가 발생하는 원인에 대해 기술하세요.
```text
<Directory></Directory> 항목을 제대로 지정하지 않아서입니다.
```

---

## 문제 4. 보안성 강화 응용프로그램 환경 설정( php.ini ) 능력

### 문제 4-1. 서버의 정보 유출을 방지하기 위해 에러 메세지를 브라우저에 표시하지 않도록 설정하는 것을 기술하시오
```text
display_errors = Off
```

### 문제 4-2. 세션 쿠키를 JavaScript에서 접근하지 못하게 설정하는 것을 기술하시오
```text
session.cookie_httponly = On
```
