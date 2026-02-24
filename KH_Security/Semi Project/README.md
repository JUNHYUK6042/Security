# Semi Project

## 주제 : WebShell 기반 파일 업로드 취약점을 이용한 권한 상승과 Database 탈취 및 대응방안

## 프로젝트 목적
- 파일 업로드 취약점을 이용한 WebShell 업로드
- 서버 권한 상승 과정 분석
- 데이터베이스 탈취 시나리오 구현
- 보안 대응 방안 설계

---

## 웹 사이트 구조

```text
WebApp/
│
├── app/                              # 서버 로직 처리 영역 (public에서 include로만 사용)
│   ├── dbconn.php                    # DB 연결 모듈 (공통 include)
│   ├── login_process.php             # 로그인 처리 (login.html의 form action)
│   ├── register_process.php          # 회원가입 처리 (register.html의 form action)
│   ├── write_process.php             # 글작성 + 파일 업로드 처리 (write.html의 form action)
│   └── upload_process.php            # (현재 흐름상 미사용 가능성 높음: write는 write_process로 감)
│
├── public/                           # DocumentRoot (브라우저로 직접 접근)
│   ├── index.php                     # 메인(로그인/회원가입 링크)
│   ├── login.php                     # include: ../app/dbconn.php, html/login.html
│   ├── register.php                  # include: ../app/dbconn.php, html/register.html
│   ├── board.php                     # 게시판 목록(페이지 자체가 PHP로 렌더링)
│   ├── write.php                     # 로그인 체크 후 html/write.html include
│   │
│   ├── css/
│   │   └── style.css                 # 공통 스타일
│   │
│   ├── upload/                       # 업로드 파일 저장 경로
│   │   └── (write_process.php가 ../public/upload/ 로 move_uploaded_file 수행)
│   │
│   └── html/
│       ├── login.html                # form action="/app/login_process.php"
│       ├── register.html             # form action="/app/register_process.php"
│       ├── write.html                # form action="/app/write_process.php" enctype="multipart/form-data"
│       └── board.html                # (현재 board.php가 직접 렌더링해서 미사용 가능성 높음)

```

---

## 실습 환경 및 기술 스택 정리

## 서버 환경 구성

## 운영체제
```text
- OS : Rocky Linux 8.10
- IP : 192.168.10.149
```

## DNS 서버
```text
- 서비스 : BIND 9.11.36
- IP : 192.168.10.148
```

- 역할 :
  - 도메인 → IP 주소 변환
  - 내부 네임 해석 처리

## 웹 서버
```text
- Web Server : Apache HTTP Server 2.4.37
- 설치 환경 : Rocky Linux 기본 httpd
- IP : 192.168.10.149
```

- 역할 :
  - HTTP 요청 수신
  - PHP 스크립트 처리
  - 파일 업로드 처리
  - 게시판 서비스 제공

## 데이터베이스 서버
```text
- DBMS : MariaDB 8.0.44
- IP : 192.168.10.149
- 계열 : MySQL 기반 DBMS
```

- 역할 :
  - 사용자 정보 저장
  - 게시판 데이터 저장
  - 인증 로직 처리

## 웹 애플리케이션 개발 환경
```text
- Frontend
  - HTML5
  - CSS

- Backend
  - PHP 7.2.24
```

- 주요 기능 :
  - 로그인 / 회원가입
  - 게시판 CRUD
  - 파일 업로드 기능
  - 세션 기반 인증 처리


## 패킷 캡처 및 공격 분석 도구
```text
Burp Suite Community Edition 2026.2.1
```

- 역할 :
  - HTTP 요청 가로채기
  - 파라미터 변조
  - 파일 업로드 요청 수정
  - SQL Injection 테스트

---

- 프로젝트 진행 상황에 따라 추가로 작성예정
