# DNS 서버 정리 (Linux / BIND 기준)

---

## 1. DNS 개요

### 한마디 정의  
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

## 2. DNS 구조 (이름 해석 과정)

### www.amazon.com 조회 시 동작 순서
1. Client → Local DNS
2. Local DNS → Root DNS (com DNS 서버 위치 질의)
3. Local DNS → TLD DNS (.com)
4. Local DNS → Authoritative DNS (amazon.com)
5. 최종 IP 주소 반환

---

## 3. DNS 서버 종류

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

## 4. Cache DNS 서버 구성

### 설치
```bash
dnf install -y bind bind-utils
```

### 주요 파일
- 데몬: `/usr/sbin/named`
- 설정 파일: `/etc/named.conf`
- 캐시 파일: `/var/named/named.ca`

### named.conf 설정
```conf
options {
    directory "/var/named";
};

zone "." {
    type hint;
    file "named.ca";
};
```

### 서비스 시작
```bash
systemctl start named
systemctl status named
```

### 테스트
```bash
nslookup www.google.com 192.168.10.31
```

---

## 5. Authoritative DNS 서버 구성

### 특징
- 자체 도메인을 관리
- Zone 파일 필요

### named.conf 설정
```conf
zone "." {
    type hint;
    file "named.ca";
};

zone "te.sec." {
    type master;
    file "te.sec.zone";
};
```

---

## 6. Zone 파일 (te.sec.zone)

### 기본 구조
```zone
$TTL 1D
@ IN SOA ns.te.sec. root.ns.te.sec. (
    0   ; Serial
    1D  ; Refresh
    1H  ; Retry
    1W  ; Expire
    3H  ; Minimum
)

IN NS ns.te.sec.
IN A 192.168.10.31

ns   IN A 192.168.10.31
mail IN A 192.168.10.32
www  IN CNAME mail
```

### 주의 사항
- Zone 수정 시 **Serial 반드시 증가**

---

## 7. Resource Record 종류

- A     : 호스트 → IPv4
- AAAA  : 호스트 → IPv6
- CNAME : 별칭
- MX    : 메일 서버
- NS    : 네임서버
- PTR   : 역방향 조회

---

## 8. 다중 도메인 서버

### named.conf에 zone 추가
```conf
zone "te.itc." {
    type master;
    file "te.itc.zone";
};
```

---

## 9. Forwarder 설정

### 전체 포워딩
```conf
options {
    forwarders { 8.8.8.8; };
    forward only;
};
```

### 영역별 포워딩
```conf
zone "itclass.co.kr." {
    type forward;
    forwarders { 8.8.8.8; };
    forward only;
};
```

---

## 10. Slave DNS 서버

### Master 설정
```conf
zone "te.sec." {
    type master;
    file "te.sec.zone";
    also-notify { 192.168.10.32; };
    allow-transfer { 192.168.10.32; };
};
```

### Slave 설정
```conf
zone "te.sec." {
    type slave;
    file "te.sec.zone";
    masters { 192.168.10.31; };
};
```

### Zone Transfer 방식
- IXFR: 변경된 부분만 전송 (기본)
- AXFR: 전체 Zone 전송

---

## 11. Domain Zone Transfer 보안

### 공격 방지 설정
```conf
options {
    allow-transfer { 192.168.10.32; };
};
```

---

## 12. 도메인 위임 (Delegation)

### 개념
- 상위 도메인이 하위 도메인의 권한을 위임

### 설정 예시 (te.sec → st.te.sec)
```zone
st.te.sec.    IN NS ns.st.te.sec.
ns.st.te.sec. IN A 192.168.10.32
```

### 절차
1. 상위 zone 파일에 NS/A 레코드 추가
2. 하위 DNS 서버 구성
3. 상위 서버 기준 테스트

---
