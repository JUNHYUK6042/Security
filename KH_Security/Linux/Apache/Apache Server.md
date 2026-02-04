# Apache Server

## Apache 개요
- HTTP 기반 웹 서버
- Client / Server 구조
- HTTP/1.0(RFC1945), HTTP/1.1(RFC2068)
- 오픈소스, GNU GPL
- 공식 사이트: https://www.apache.org

---

## Apache 설치

### 사전 패키지
```text
  dnf install -y gcc gcc-c++ cmake apr apr-util zlib-devel wget net-tools expat-devel
```

### 소스 설치
```text
1. cd /usr/local : 관리자가 직접 설치한 소프트웨어 전용 공간입니다.
2. wget https://archive.apache.org/dist/httpd/httpd-2.2.34.tar.gz
  - wget : Apache 2.2.34 소스 코드를 인터넷에서 다운로드 합니다.
  - .tar.gz : 압축된 소스 코드로 묶습니다.
3. tar xvfz httpd-2.2.34.tar.gz : 압축된 소스 코드를 풀어서 디렉토리로 만듭니다.
4. cd httpd-2.2.34 : Apache 소스 코드 디렉토리로 이동 합니다.
5. ./configure --prefix=/app/apache --enable-so
  - ./configure : 시스템 라이브러리, 컴파일러, 의존성 체크합니다.
  - --prefix=/app/apache : Apache 설치 경로 지정합니다.
  - --enable-so : DSO(Dynamic Shared Object) 활성화합니다.
    - 없을 시 모듈 로딩 불가
6. make : 소스 코드를 컴파일 해서 실행 파일을 만듭니다.
7. make install : 컴파일된 결과물을 실제 설치 경로로 복사합니다.
```

---

## 주요 경로


- `실행 데몬: /app/apache/bin/httpd`
  - httpd는 실제로 웹 요청을 처리하는 Apache의 본체 데몬이다
- `제어 스크립트: /app/apache/bin/apachectl`
  - Apache를 안전하게 시작·중지·재시작하는 관리자용 스크립트입니다.
  ```text
  /app/apache/bin/apachectl start - 
  /app/apache/bin/apachectl stop
  /app/apache/bin/apachectl restart
  ```
- `설정 파일: /app/apache/conf/httpd.conf`
  - Apache의 모든 동작 규칙을 정의하는 파일
- `기본 페이지: /app/apache/htdocs/index.html`


---

## httpd.conf 문법 검사
/app/apache/bin/httpd -t

## 전역 환경 설정
ServerRoot "/app/apache"
Timeout 120
MaxClients 150
StartServers 20
ServerName localhost

## 기본 서버 설정
DocumentRoot "/app/apache/htdocs"
DirectoryIndex index.html index.php
ErrorLog logs/error_log
CustomLog logs/access_log combined
ServerAdmin root@localhost

## Directory 설정
<Directory "/app/apache/htdocs">
    Options Indexes FollowSymLinks
    AllowOverride None
    Order allow,deny
    Allow from all
</Directory>

## 접근 제어 (2.2 방식)
Order deny,allow
Deny from all
Allow from 192.168.123.

## Require 방식
Require all granted
Require not ip 192.168.123.0/24

## 사용자 홈 디렉토리
<Directory "/home/*/public_html">
    Options Indexes FollowSymLinks
    AllowOverride None
    Require all granted
</Directory>

## DocumentRoot 분리
- 웹 계정: webmaster
- DocumentRoot: /home/httpd/html

## 가상 호스트 (IP 기반)
<VirtualHost 192.168.10.2>
    DocumentRoot /home/httpd/sec
    ServerName 192.168.10.2
</VirtualHost>

<VirtualHost 192.168.10.3>
    DocumentRoot /home/httpd/itc
    ServerName 192.168.10.3
</VirtualHost>

## 가상 호스트 (도메인 기반)
NameVirtualHost *:80

<VirtualHost *:80>
    DocumentRoot /home/httpd/sec
    ServerName www.example.sec
    ServerAlias example.sec
</VirtualHost>

<VirtualHost *:80>
    DocumentRoot /home/httpd/itc
    ServerName www.example.itc
    ServerAlias example.itc
</VirtualHost>

## 가상 호스트 주의사항
- DocumentRoot 권한 필수
- DNS 설정 우선
- /etc/hosts 와 hostname 일치
- httpd-vhosts.conf include 필요
