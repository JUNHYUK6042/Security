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
├── app/                    # 서버 로직 처리 영역 (웹에서 직접 접근 X)
│   ├── dbconn.php
│   ├── login_process.php
│   ├── register_process.php
│   ├── write_process.php
│   └── upload_process.php
│
├── public/                 # 웹에서 직접 접근 가능한 영역 (DocumentRoot)
│   │
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── board.php
│   ├── write.php
│   │
│   ├── css/
│   │   └── style.css
│   │
│   ├── upload/             # 파일 업로드 저장 경로 (취약 지점)
│   │
│   └── html/
│       ├── login.html
│       ├── register.html
│       ├── board.html
│       └── write.html
│
└── database.sql
```

---

- 프로젝트 진행 상황에 따라 작성예정
