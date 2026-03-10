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
