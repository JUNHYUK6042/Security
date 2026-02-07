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

## Apache Server 주요 구성 파일

- `실행 데몬: /app/apache/bin/httpd`
  - httpd는 실제로 웹 요청을 처리하는 Apache의 본체 데몬이다
- `제어 스크립트: /app/apache/bin/apachectl`
  - Apache를 안전하게 시작·중지·재시작하는 관리자용 스크립트입니다.
  ```text
  /app/apache/bin/apachectl start - Apache 서버 시작
  /app/apache/bin/apachectl stop - Apache 서버 중지
  /app/apache/bin/apachectl restart - Apache 서버 재시작
  ```
- `설정 파일: /app/apache/conf/httpd.conf`
  - Apache의 모든 동작 규칙을 정의하는 파일
  - DocumentRoot, ServerName <Directory> .... </Directory> 등을 설정
- `기본 페이지: /app/apache/htdocs/index.html`

### Apache Server 구동 

```text
/app/apache/bin/apachectl start (stop | restart)
```

- Apache Server 자동 실행
  - `/etc/rc.d/rc.local` 파일에 `/app/apache/bin/apachectl start`과  
  /app/apache/bin/apachectl stop 명령어를 넣어줍니다.
  - `/etc/rc.d/rc.local` : 시스템이 부팅 완료 단계에 들어갔을 때, 관리자가 지정한 명령어를 자동 실행해주는 파일입니다.
 
---

## Apache Server 실습

- **관리자 정보**
  - 계정 : webmaster(group:web)
  - Document Root : /home/httpd/html

### 웹 전용 그룹 및 사용자 계정 생성

```text
- 그룹 생성
  groupadd -g 2000 web

- 사용자 생성
  useradd -u 2100 webmaster
```

---

### DocumentRoot 디렉터리 및 기본 페이지 생성

Apache에서 사용할 웹 루트 디렉터리와 index.html 파일을 생성한다.

- 디렉터리 생성
```text
mkdir -p /home/httpd/html
```

- 기본 페이지 생성
```text
echo "문자열" > /home/httpd/html/index.html
```

- 소유자 변경
```text
chown -R webmaster.web /home/httpd
```
 - 웹 콘텐츠 관리 계정(webmaster)이 root 권한 없이 웹 파일을 관리하도록 하기 위해 변경해줍니다.

---

### bind mount를 이용한 디렉터리 접근 제한

- webmaster 계정이 웹 콘텐츠 디렉터리만 관리할 수 있도록  
`bind mount`를 사용하여 접근 범위를 제한한다.

```text
mount -B /home/httpd /home/webmaster/httpd
```

---

### Apache 설정 파일 설정 (기본)

- Apache 설정 파일에서 DocumentRoot를 변경하고  
해당 디렉터리에 대한 접근 설정을 추가한다.

- **설정 파일**
```text
/app/apache/conf/httpd.conf
```

- **ServerName localhost**
```text
Apache가 자기 자신을 어떤 이름의 서버로 인식할지 지정
```

- **DocumentRoot "/home/httpd/html"**
```text
DocumentRoot는 웹으로 노출되는 파일의 기준 디렉터리를 지정
```

- **Directory 접근 설정**
```text
<Directory "/home/httpd/html"> : DocumentRoot 디렉터리에 대한 접근 규칙을 정의하는 블록
    AllowOverride None
      - Apache 설정은 중앙에서만 관리하기 위해 .htaccess를 차단

    Options Indexes FollowSymLinks
      - 디렉터리 기능(목록 표시, 심볼릭 링크 사용)을 제어

    Order allow,deny
      - 접근 허용/차단 규칙의 적용 순서를 정의

    Allow from all
      - 모든 IP의 웹 접근을 허용
</Directory>
```

---

### Apache 설정 파일 설정 (가상 호스트.version)

- /home/httpd/sec, itc/index.html 실습 때 사용될 설정입니다.

- **conf/extra/httpd-vhosts.conf 파일 설정 설명**

- include 설정
  - 다음과 같은 명령어의 주석 해제를 해야합니다.
  - 주석 해제하는 이유 : **httpd.conf에서 httpd-vhosts.conf 파일을 불러오지 않을 시 적용이 되지 않기 때문입니다.**
```text
Include conf/extra/httpd-vhosts.conf 
```

- 그 이후에 다음과 같이 `httpd-vhosts.conf 파일을 설정 해주어야 합니다.
```text
NameVirtualHost *:80   ← * 대신 IP 지정이 가능하다.

<VirtualHost *:80>  
  ServerAdmin [메일 주소]  
  DocumentRoot "[Web 홈 디렉토리]"  
  ServerName [접속 도메인명]  
  ServerAlias [별명]  
  ErrorLog "logs/[에러 로그 파일명]"  
  CustomLog "logs/[접속 로그 파일명]"  
</VirtualHost>
```

#### 가상 호스트 (IP 기반)
```text
<VirtualHost 192.168.10.###>
    DocumentRoot /home/httpd/sec
    ServerName 192.168.10.###
</VirtualHost>

<VirtualHost 192.168.10.###>
    DocumentRoot /home/httpd/itc
    ServerName 192.168.10.###
</VirtualHost>
```

#### 가상 호스트 (도메인 기반)
```text
NameVirtualHost *:80

<VirtualHost *:80>
    DocumentRoot /home/httpd/sec
    ServerName www.ast06.sec
    ServerAlias ast06.sec
</VirtualHost>

<VirtualHost *:80>
    DocumentRoot /home/httpd/itc
    ServerName www.ast06.itc
    ServerAlias ast06.itc
</VirtualHost>
```

### 가상 호스트 주의사항
- DocumentRoot 권한 필수
- DNS 설정 우선
- /etc/hosts 와 hostname 일치
- httpd-vhosts.conf include 필요


---

## 서비스 재시작

- 설정 적용을 위해 Apache 서비스를 재시작한다.
```text
/app/apache/bin/apachectl restart
```
---

## 실습 결과 확인

---
