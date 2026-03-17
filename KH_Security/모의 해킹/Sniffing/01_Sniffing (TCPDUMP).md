# Sniffing (TCPDUMP)

## 개요

- 본 문서는 네트워크에서 발생할 수 있는 **Sniffing 공격의 원리와 동작 방식**을 이해하고 이를 실습을 통해 확인하는 것을 목표로 합니다.  
- tcpdump, dsniff 등의 도구를 이용하여 네트워크 패킷을 수집하고 분석하며,  
Telnet과 같은 평문 통신에서 로그인 정보가 어떻게 노출되는지를 확인합니다.
- 스위치 환경에서 스니핑을 가능하게 만드는 **ARP Spoofing 및 ICMP Redirect 공격 기법**을 통해  
공격자가 네트워크 중간에서 트래픽을 가로채는 과정을 이해하고, 이러한 공격에 대한 보안 위험성과 대응 방법을 학습합니다.

---

## Sniffing 정의

- 스니핑(Sniffing)이란? 
```
네트워크를 통해 전송되는 패킷 데이터를 가로채서 내용을 분석하는 행위를 의미합니다.

즉 공격자는 네트워크 상에서 흐르는 다른 사용자의 트래픽까지 분석할 수 있습니다.
- 트래픽(Traffic) : 네트워크에서 오가는 모든 데이터 흐름(패킷들의 이동)을 의미합니다.
```

---

## 스니핑 도구 정리

| 도구명 | 특징 | 사용 예시 |
|-------|------|---------|
| tcpdump | CLI 기반 패킷 캡처 도구, 필터링 기능 제공 | 특정 포트(예: telnet) 패킷 캡처 |
| Wireshark | GUI 기반 패킷 분석 도구, 상세 분석 가능 | 전체 네트워크 트래픽 시각적 분석 |
| dsniff | 다양한 프로토콜의 인증 정보 자동 추출 | telnet, ftp 계정 정보 확인 |
| Ettercap | MITM 공격과 스니핑 기능 지원 | ARP spoofing + 패킷 가로채기 |
| Driftnet | 네트워크에서 전송되는 이미지 추출 | HTTP 이미지 스니핑 |
| NetworkMiner | 패킷 기반 포렌식 분석 도구 | 파일 및 세션 복원 분석 |
| Tshark | Wireshark의 CLI 버전 | 서버 환경 자동화 패킷 분석 |

---

## Sniffing 설정(모드)

### Promiscuous mode

- 프러미스큐어스 모드는 **스니핑 도구가 아니라 네트워크 인터페이스 카드(NIC)의 동작 모드**입니다.  
일반적으로 NIC는 자신에게 전달된 패킷만 수신하지만, 프러미스큐어스 모드가 활성화되면 **자신의 목적지가 아닌 패킷도 함께 수신**할 수 있습니다.
- 즉, 프러미스큐어스 모드는 **스니핑을 가능하게 해주는 설정**이고,  
실제로 패킷을 캡처하고 분석하는 것은 **tcpdump, Wireshark, dsniff 같은 스니핑 도구**가 수행합니다.

- 다음 명령어를 통해 장치의 프러미스큐어스 모드를 활성화 및 비활성화 합니다.
```
ip link set dev eth0 promisc on/off

ifconfig eth0 [+/-] promisc
```

- `ip a show eth0` 명령어를 통해 활성화 상태를 확인해줍니다.

![Promiscuous mode](/KH_Security/모의%20해킹/Sniffing/img/Promiscuous%20mode.png)

- 위의 결과처럼 `PROMISC`가 있으므로 Promiscuous Mode가 활성화 됐다는 것을 알 수 있습니다.

---

## Sniffing (TCPDUMP 도구)

- Tcpdump는 네트워크에서 흐르는 패킷을 캡처하고 분석하는 **CLI 기반 스니핑 도구**입니다.   
네트워크 관리 및 분석 도구인 Snort의 기반이 되며, 다양한 형태로 패킷을 확인할 수 있습니다.

### 주요 기능

- 네트워크에서 전송되는 모든 패킷을 캡처 가능
- 패킷의 헤더 또는 전체 데이터 확인 가능
- 특정 포트, IP, 프로토콜 기준으로 필터링 가능
- 캡처한 패킷을 파일로 저장 가능

### 주요 옵션 정리

| 옵션 | 설명 |
|------|------|
| -i | 사용할 네트워크 인터페이스 지정 |
| -v / -vv / -vvv | 출력 정보 상세 증가 |
| -x | 패킷을 16진수(hex)로 출력 |
| -X | hex + ASCII 출력 |
| -A | ASCII 형태로 출력 |
| -w | 패킷을 파일로 저장 |
| -r | 저장된 파일 읽기 |
| -c | 지정한 개수만큼 캡처 후 종료 |
| -q | 간략 정보 출력 |
| -D | 사용 가능한 인터페이스 출력 |
| -e | Ethernet 헤더 포함 출력 |

### 필터 정리

| 필터 종류 | 사용법 | 설명 |
|----------|--------|------|
| IP | src 192.168.0.10 | 출발지 IP |
| IP | dst 192.168.0.10 | 목적지 IP |
| IP | host 192.168.0.10 | 출발지 또는 목적지 |
| MAC | ether src [MAC] | 출발지 MAC |
| MAC | ether dst [MAC] | 목적지 MAC |
| MAC | ether host [MAC] | 출발지 또는 목적지 MAC |
| 네트워크 | net 192.168.10.0/24 | 특정 네트워크 |
| 포트 | port 80 | 특정 포트 |
| 포트 | src port 22 | 출발지 포트 |
| 포트 | dst port 22 | 목적지 포트 |
| 포트 | portrange 1-1024 | 포트 범위 |
| 프로토콜 | tcp / udp / icmp / arp | 프로토콜 필터 |
| 조건 | and / or / not | 조건 결합 |

### 사용 예시

| 기능 | 명령어 |
|------|--------|
| 기본 캡처 | tcpdump -i eth0 -X |
| 특정 IP | tcpdump -i eth0 -X host 192.168.0.10 |
| 출발지 IP | tcpdump -i eth0 -X src 192.168.0.10 |
| 목적지 IP | tcpdump -i eth0 -X dst 192.168.0.10 |
| 특정 포트 | tcpdump -i eth0 -X port 80 |
| TCP 필터 | tcpdump -i eth0 -X tcp |
| UDP 필터 | tcpdump -i eth0 -X udp |
| ICMP 필터 | tcpdump -i eth0 -X icmp |
| 복합 필터 | tcpdump -i eth0 -X src 192.168.0.10 and port 22 |
| 패킷 수 제한 | tcpdump -i eth0 -X -c 100 |
| 파일 저장 | tcpdump -i eth0 -w capture.pcap |
| 파일 읽기 | tcpdump -r capture.pcap |

---

## TCPDUMP를 이용한 스니핑 실습

### 실습 구성도

![구성](/KH_Security/모의%20해킹/Sniffing/img/TCPDUMP%20구성.png)

---

### 실습

#### 192.168.11.36 (공격자)

- 192.168.11.36에서 다음과 같은 명령어를 통해 tcpdump를 실행해줍니다.
- 다음 명령어는 eth0 인터페이스에서 Telnet(23번 포트) 관련 패킷 중 192.168.11.17과 통신하는 패킷을 캡처하여   
hex + ASCII 형태로 출력하고 결과를 파일(11.17)에 저장하는 명령어입니다.

```
tcpdump -xX -i eth0 tcp port 23 and host 192.168.11.17 > 11.17
```

---

#### 192.168.11.17 (Telnet 서버)

- Telnet 서버를 구성한 뒤 다음 명령어를 통해 활성화 합니다.
```
systemctl start Telnet
```

---

#### 192.168.11.7 (클라이언트)

- 클라이언트에서 다음과 같은 명령어를 통해 Telnet 서버로 접속을 합니다.
```
telnet 192.168.11.17
```

---

#### 캡쳐 결과 확인

- ID : st04

![ID](/KH_Security/모의%20해킹/Sniffing/img/Sniffing%20tcpdump.png)

- PASSWORD : jun@6042

![PASSWD1](/KH_Security/모의%20해킹/Sniffing/img/Sniffing%20tcpdump%20passwd-1.png)  

![PASSWD2](/KH_Security/모의%20해킹/Sniffing/img/Sniffing%20tcpdump%20passwd-2.png)

- 해당 패킷 캡처 결과는 Telnet 통신 과정에서 입력된 사용자 정보(ID 및 Password)가  
네트워크 상에서 **암호화되지 않은 평문(Plain Text)** 형태로 전송되는 모습을 보여줍니다.

- Telnet 프로토콜은 데이터를 암호화하지 않기 때문에 사용자가 입력한 ID와 Password가  
패킷 내부에 그대로 포함되며, tcpdump를 통해 캡처한 패킷의 ASCII 영역에서 이를 확인할 수 있습니다.

- 이러한 결과는 공격자가 네트워크 상에서 패킷을 스니핑할 경우 사용자의 인증 정보가 쉽게 탈취될 수 있음을 의미합니다.
