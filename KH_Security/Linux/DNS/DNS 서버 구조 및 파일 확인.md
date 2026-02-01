# DNS 서버 정리 (Linux / BIND 기준)

## DNS 개요

DNS는 **호스트 이름을 IP 주소로 변환해주는 분산 데이터베이스 시스템**이다.

- 분산 Database 구조 (계층형)
- Application Layer Protocol
- HTTP, FTP, SMTP 등에서 호스트명을 IP로 변환할 때 사용
- Network Edge에 구현
- IP ↔ Name 매핑 (역방향도 가능)

### 중앙집중식 DNS를 쓰지 않는 이유
- 단일 장애 지점 발생
- 트래픽 집중
- 거리 문제
- 관리 복잡성

---

## DNS 구조 (이름 해석 과정)

### www.amazon.com 조회 시 동작 순서

```text
1. Client → Local DNS 서버에게 www.amazon.com의 ip를 질의함
2. Local DNS 서버 → Root DNS 서버에게 com DNS 서버의 ip를 질의함
3. Root DNS 서버 → Local DNS 서버에게 com DNS 서버의 ip를 응답함
4. Local DNS 서버 → com DNS 서버에게 amazon.com DNS 서버의 ip를 질의함
5. com DNS 서버 → Local DNS 서버에게 amazon.com DNS 서버의 ip를 응답함
6. Local DNS 서버 → amazon.com DNS 서버에게 www.amazon.com DNS 서버의 ip를 질의함
7. amazon.com DNS 서버 → Local DNS 서버에게 www.amazon.com DNS 서버의 ip를 응답함
8. Local DNS 서버 → Client에게 www.amazon.com의 ip를 응답함
```

---

## DNS 서버 종류

### Root Name Server
- Authoritative DNS 서버의 위치만 알려줌
- 실제 IP는 반환하지 않음

### TLD Server
- com, net, org, edu, 국가 도메인 담당
- 예: `.com`, `.net`, `.edu`

### Authoritative DNS Server
- 실제 도메인 정보(IP 매핑)를 보유
- 1차(Master), 2차(Slave) 서버 존재

### Cache DNS Server (Local DNS)
- 계층 구조에 속하지 않음
- 질의 결과를 캐시에 저장
- Root/TLD 서버 접근 횟수 감소
- 주로 Local DNS 서버로 사용

---

## BIND 패키지 설치 확인

### 설치

- 설치 가능한 BIND 관련 패키지를 확인합니다.
```text
dnf list bind bind-utils
```

- 필자는 이미 다음 명령어로 패키지를 설치하였습니다.
```text
dnf install -y bind bind-utils
```

- 설치가 완료되면 다음과 같이 설치된 패키지 목록이 출력됩니다.  
![01](/KH_Security/Linux/DNS/img/01.png)

---

## 주요 파일

### 데몬 : `/usr/sbin/named` 역할

``` text
- 클라이언트의 DNS 질의를 수신 (UDP/TCP 53)
- /etc/named.conf를 읽어서
  - 어떤 도메인을 관리할지
  - 어떤 존 파일을 쓸지
- /var/named에 있는 존 파일을 메모리에 로딩
- 질의에 대해 권한 응답(authoritative) 또는 재귀 응답(recursive) 수행
```

---

### named.conf 설정 파일 확인

```text
cat /etc/named.conf
```

![02](/KH_Security/Linux/DNS/img/02.png)

#### 설정 파일 : `/etc/named.conf` 역할

- `options { directory     "/var/named"; };`
```text
zone 파일의 위치는 /var/named/ 에 위치 했다는 것을 알려줌
```

- `zone "sec." { type master; file "sec.zone"; };`
```text
sec. : 도메인 이름
type master : 존 파일(sec.zone)을 직접 수정
    - 다른 slave 서버가 있으면 이 서버에서 존 전송(AXFR/IXFR)
sec.zone : 이 존의 실제 데이터가 들어있는 파일
    - sec. 도메인에 대한 정보를 sec.zone 파일에서 확인
```

---

### named.ca 파일

- Root DNS 서버의 정보를 담고 있는 파일입니다.

```text
cat /var/named/named.ca
```

![03](/KH_Security/Linux/DNS/img/03.png)

#### named.ca 파일 역할
```ex
.                       518400  IN  NS  a.root-servers.net.
a.root-servers.net.     518400  IN  A   198.41.0.4
```
- `.` : 루트 도메인(DNS 최상위)
- `NS` : 루트 도메인을 관리하는 네임서버 ( a.root-servers.net )
  - DNS의 시작은 a.root-servers.net에게 질의
- `a.root-servers.net.     518400  IN  A   198.41.0.4` : 실제 IP 주소를 알려줌

---

## named 데몬 제어

```bash
systemctl start named : named 시작
systemctl status named : named 상태 확인
systemctl restart named : named 재시작
systemctl stop named : named 종료
```

![04](/KH_Security/Linux/DNS/img/04.png)

---

## nslookup 실습

```bash
nslookup www.google.com 192.168.35.214
```

- `nslookup` : 도메인 이름과 IP 주소를 서로 변환해보며 DNS 동작을 확인하는 조회 도구입니다.  
![05](/KH_Security/Linux/DNS/img/05.png)

- www.google.com의 IP 주소를 Local DNS 서버(192.168.35.214)에게 질의를 한 것입니다.
- 그 뒤에 Local DNS 서버가 클라이언트에게 www.google.com의 IP 주소를 응답해 준 것입니다.

---
