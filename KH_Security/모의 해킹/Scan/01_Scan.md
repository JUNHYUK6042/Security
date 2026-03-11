# Scan

## 개요

- 스캔(scan)은 네트워크에서 대상 시스템의 상태를 확인하고 정보를 수집하기 위한 과정이다.

- 스캔을 통해 다음과 같은 정보를 얻을 수 있다.
  - 열려 있는 포트
  - 실행 중인 서비스
  - 데몬 버전
  - 운영체제(OS) 정보
  - 취약점

- TCP/IP 기반 서비스는 기본적으로 Request → Response 구조로 동작하기 때문에 이 메커니즘을 이용하여 스캔을 수행한다.

---

## 주요 포트와 서비스

| Port | Service | 설명 |
|-----|-----|-----|
| 21 | FTP | 파일 전송 서비스 |
| 23 | Telnet | 원격 서버 접속 |
| 25 | SMTP | 메일 전송 |
| 53 | DNS | 도메인 이름 해석 |
| 69 | TFTP | 인증 없는 파일 전송 |
| 80 | HTTP | 웹 서비스 |
| 110 | POP3 | 메일 수신 |
| 111 | RPC | 원격 프로세스 실행 |
| 138 | NetBIOS | Windows 파일 공유 |
| 143 | IMAP | 서버에 메일을 남기는 메일 프로토콜 |
| 161 | SNMP | 네트워크 관리 |

---

## Ping & ICMP Scan

- Ping은 **네트워크와 시스템이 정상적으로 동작하는지 확인하기 위한 간단한 유틸리티**이다.
- Ping은 **ICMP (Internet Control Message Protocol)** 를 사용한다.
- 각 네트워크에는 고유한 ping이 존재하지만 일반적으로 말하는 ping은 **TCP/IP 네트워크의 ping**을 의미한다.

---

## ICMP Scan

- ICMP를 이용한 스캔 방법으로는 다음의 네 가지를 생각할 수 있습니다.
```
- Echo Request(Type 8)과 Echo Reply(Type 0)을 이용한 방법
- Timestamp Request(Type 13)와 Timestamp Reply(Type 14)을 이용한 방법
- Information Request(Type 15)와 Information Reply(Type 16)을 이용한 방법
- ICMP Address Mask Request(Type 17)와 ICMP Address Mask Reply(Typ18)을 이용한 방법
```

### Ping을 이용한 scan

![ping](/KH_Security/모의%20해킹/Scan/img/Ping.png)

- `1 : 전송 패킷 길이`
- `2 : 응답 패킷 길이
- `3 : 응답 시간`
- `4 : TTL 값`
  - 리눅스(64), Windows(128), 솔라리스(255)
- `5 : 전송 패킷 수`
- `6 : 응답 패킷 수`

### ICMP 주요 메시지 타입

| Type | 설명 |
|----|----|
| 0 | Echo Reply |
| 3 | Destination Unreachable |
| 5 | Redirect |
| 8 | Echo Request |
| 11 | Time Exceeded |
| 13 | Timestamp |
| 14 | Timestamp Reply |
| 17 | Address Mask Request |
| 18 | Address Mask Reply |

---

## TCP scan

- TCP Scan은 **특정 포트가 열려있는지 확인하는 스캔 방식**이다.

### TCP Open Scan
- TCP 3-way handshake를 이용하는 스캔

---

## Stealth Scan (Half Open Scan)

- 3-way handshake를 완전히 수행하지 않는 스캔 방식
- 3W HS과정에서 RST 패킷을 이용 포트를 확인하고 connect는 생성하지 않는다.
- 세션을 확정하지 않기 때문에 로그정보를 남기지 않는다.
- 일반적인 stealth Scan은 로그를 남기지 않는것 뿐아니라 자신을 숨기는 모든 scan을 통칭한다


![TCP Half Open](/KH_Security/모의%20해킹/Scan/img/TCP%20Half%20Open.png)

### 열린 포트
```
SYN →
SYN/ACK ←
RST →
```

### 닫힌 포트
```
- SYN →
- RST/ACK ←
```

### 특징
```
- 연결을 완전히 생성하지 않음
- 로그 탐지를 회피할 수 있음
```

---

## Stealth Scan (FIN / NULL / XMAS Scan)

- 모든 포트에 ACK 패킷전송- 열린 포트 : TTL은 64이하, Rwin size는 0 보다 큰 RST 패킷 응답
- 닫힌 포드 : TTL은 큰값(OS에 따라 다름), Rwin size는 0인 RST 패킷
- 현재 까지 매우 유용한 방법입니다.


![FIN/NULL/XMAS](/KH_Security/모의%20해킹/Scan/img/FIN%2C%20NULL%2C%20XMAS.png)

### 동작 방식
```
- 공격자는 대상 시스템의 포트로 **FIN / NULL / XMAS 패킷**을 전송한다.

FIN Scan
- FIN flag 설정

NULL Scan
- flag 없음

XMAS Scan
- FIN + PSH + URG flag 설정
```

### 특징
```
- TCP 3-way handshake를 사용하지 않는다.
- 방화벽이나 IDS를 우회하기 위한 stealth scan 방식이다.
- 정상적인 TCP 패킷이 아니기 때문에 일부 시스템에서는 탐지하기 어렵다.
```

---

## ACK Scan

- ACK 패킷을 이용하여 포트 상태 분석
- 응답 패킷의 TTL, Window Size 등을 통해 포트 상태를 추정한다.


![ACK](/KH_Security/모의%20해킹/Scan/img/ACK.png)

### 동작 방식
```
공격자 → ACK 패킷 전송 → 대상 시스템 응답으로 RST 패킷이 돌아오며  
RST 패킷의 **TTL 값과 Window Size(Rwin)** 를 분석한다.
```

### 특징
```
- ACK 패킷을 이용한 stealth scan
- TCP 연결을 생성하지 않는다.
- 방화벽 규칙 분석에 유용하다.
- 현재까지도 유용하게 사용되는 스캔 방식이다.
```

---

## UDP Scan

- UDP Scan은 대상 시스템의 **UDP 포트 상태를 확인하는 스캔 방식**이다.
- TCP와 달리 UDP는 **3-way handshake 과정이 없기 때문에** 응답 패킷의 존재 여부를 통해 포트 상태를 판단한다.

![UDP](/KH_Security/모의%20해킹/Scan/img/UDP.png)

### 동작 방식
```
공격자는 대상 시스템의 특정 포트로 **UDP 패킷**을 전송한다.
이후 대상 시스템의 응답을 통해 포트 상태를 판단한다.
```

### 특징
```
- UDP는 handshake 과정이 없다.
- 응답이 없는 경우가 많아 스캔 속도가 느리다.
- 방화벽에서 ICMP 메시지를 차단하면 판단이 어려울 수 있다.
```
