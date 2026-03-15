# Scan

## Scan 실습

- 이 문서는 Scan 실습에 대한 문서입니다.

---

## 다양한 Scanner 설치

- 다음 명령어를 입력 시 스캐닝 도구가 설치 되어잇는지 확인해줍니다.

![01](/KH_Security/모의%20해킹/Scan/img/01.png)

- 위와 같이 전부 스캐닝 도구가 설치되어 있는 것을 알 수 있습니다.

---

## fping 실습
- fping은 여러 시스템의 **alive 여부(Host가 살아있는지)** 를 빠르게 확인하기 위한 네트워크 스캔 도구이다.
- 일반적인 ping과 달리 **여러 IP 주소를 동시에 스캔할 수 있다.**

### 특징
- 여러 host에 대해 ping을 동시에 수행
- 네트워크 대역 스캔 가능
- 빠른 host discovery 가능
- Ping Scan 용도로 많이 사용

![fping](/KH_Security/모의%20해킹/Scan/img/fping.png)

- fping 명령어를 통해 살아있는 호스트들의 여부를 파악할 수 있습니다.

---

## hping3

- hping3는 다양한 TCP/IP 패킷을 직접 생성하여 전송할 수 있는 네트워크 분석 및 스캔 도구이다.  
- ICMP, TCP, UDP 패킷을 생성할 수 있으며 포트 스캔, 방화벽 테스트, 네트워크 분석 등에 사용된다.

### 실습 목적
hping3를 이용하여 대상 시스템의 특정 포트가 **열려있는지(Open)** 또는 **닫혀있는지(Closed)** 확인한다.

### 명령어
```
hping3 [option] [target_ip]
```

| 옵션 | 설명 | 예시 |
|---|---|---|
| -1 | ICMP 패킷 생성 | hping3 -1 192.168.11.17 |
| -S | SYN 패킷 생성 | hping3 -S -p 80 192.168.11.17 |
| -A | ACK 패킷 생성 | hping3 -A -p 80 192.168.11.17 |
| -F | FIN 패킷 생성 | hping3 -F -p 80 192.168.11.17 |
| -R | RST 패킷 생성 | hping3 -R -p 80 192.168.11.17 |
| --udp | UDP 패킷 생성 | hping3 --udp -p 53 192.168.11.17 |
| -p port# | 목적지 포트 지정 | hping3 -S -p 80 192.168.11.17 |
| -s port# | 출발지 포트 지정 | hping3 -S -p 80 -s 12345 192.168.11.17 |
| --flood | 최대 속도로 패킷 생성 | hping3 -S --flood -p 80 192.168.11.17 |
| -c # | 보낼 패킷 개수 지정 | hping3 -S -c 5 -p 80 192.168.11.17 |


### TCP SYN Scan (Port 22) 실습

```
hping3 -S -p 22 192.168.11.17
```

![02](/KH_Security/모의%20해킹/Scan/img/02.png)

- 결과
```
flags=SA
```

- SYN 패킷을 전송했을 때 **SYN+ACK 응답이 돌아왔습니다.**
- 이는 대상 시스템이 연결을 허용한다는 의미이고,  
**22번 포트가 OPEN 상태**라는 의미입니다.

---

### TCP SYN Scan (Port 80) 실습

```
hping3 -S -p 80 192.168.11.17
```

![03](/KH_Security/모의%20해킹/Scan/img/03.png)

- 결과
```
flags=RA
```

- SYN 패킷을 보냈지만 **RST+ACK 응답이 돌아왔습니다.**
- 이는 대상 시스템이 해당 포트 연결을 거부했다는 의미이며,  
**80번 포트가 CLOSED 상태**라는 의미입니다.

---

### UDP Scan (Port 53) 실습

```
hping3 --udp -p 53 192.168.11.17
```

![04](/KH_Security/모의%20해킹/Scan/img/04.png)

- 결과
```
ICMP Port Unreachable
```

- UDP 패킷을 보냈을 때 **ICMP Port Unreachable 메시지가 반환되었다.**
- 이는 대상 시스템에서 해당 UDP 포트를 사용하지 않는다는 의미이며,  
**UDP 53 포트는 CLOSED 상태**입니다.

---

#### UDP Scan (Port 800) 실습

```
hping3 --udp -p 800 192.168.11.17
```

![05](/KH_Security/모의%20해킹/Scan/img/05.png)

- 결과
```
ICMP Port Unreachable
```

- 동일하게 ICMP Port Unreachable 응답이 돌아왔습니다.  
따라서 **UDP 800 포트도 CLOSED 상태**입니다.

---

## Wireshark 패킷 분석 (hping3)

### SYN Scan 패킷 흐름 (OPEN Port)

```
hping3 -S -p 80 192.168.11.17
```

![06](/KH_Security/모의%20해킹/Scan/img/06.png)
![07](/KH_Security/모의%20해킹/Scan/img/07.png)

```
192.168.11.36 → 192.168.11.17   SYN
192.168.11.17 → 192.168.11.36   SYN/ACK
```

- 이는 **TCP 3-way handshake의 첫 단계**입니다.  
즉 **포트가 OPEN 상태임을 의미합니다.**

### SYN Scan (Closed Port)

```
hping3 -S -p 80 192.168.11.17
```

![08](/KH_Security/모의%20해킹/Scan/img/08.png)
![09](/KH_Security/모의%20해킹/Scan/img/09.png)

```
192.168.11.36 → 192.168.11.17   SYN
192.168.11.17 → 192.168.11.36   RST/ACK
```


- 서버가 연결을 거부했기 때문에 RST 패킷을 반환합니다.  
즉 **포트가 CLOSED 상태입니다.**

---

### FIN Scan (OPEN Port)

- FIN Scan 특징
  - OPEN → 응답 없음
  - CLOSED → RST

```
hping3 -F -p 80 192.168.11.17
```

![10](/KH_Security/모의%20해킹/Scan/img/10.png)
![11](/KH_Security/모의%20해킹/Scan/img/11.png)


```
78 packets transmitted
0 packets received
```
- FIN 패킷을 전송했지만 응답이 없습니다.
- 이 실습에서는 응답이 없기 때문에 **방화벽 또는 필터링 가능성**이 존재합니다.
  - OPEN 포트
  - 방화벽 필터링
- FIN 패킷은 연결 종료용 패킷이라서연결이 없는 상태에서 갑자기 FIN이 오면  
정상적인 상황이 아니므로 FIN 패킷이 오면 무시해서 응답을 하지 않습니다.

### FIN Scan 패킷 분석 (Closed Port)

![12](/KH_Security/모의%20해킹/Scan/img/12.png)
![13](/KH_Security/모의%20해킹/Scan/img/13.png)

- FIN 패킷에 대해 RST 응답이 돌아왔다.
```
192.168.11.36 → 192.168.11.17   FIN
192.168.11.17 → 192.168.11.36   RST
```

- 즉 RST가 응답 왔으므로 **포트가 CLOSED 상태**이다.

---

### ACK Scan

```
hping3 -A -p 80 192.168.11.17
```

![14](/KH_Security/모의%20해킹/Scan/img/14.png)
![15](/KH_Security/모의%20해킹/Scan/img/15.png)  

![16](/KH_Security/모의%20해킹/Scan/img/16.png)
![17](/KH_Security/모의%20해킹/Scan/img/17.png)


- ACK 패킷을 보냈을 때 RST 응답이 돌아왔다.
```
ACK → RST
```

- ACK Scan은 **포트 상태(Open/Closed)가 아니라 방화벽 존재 여부를 확인하는 스캔**이며,  
RST 응답(flags=R)이 돌아왔다는 것은 **방화벽에 의해 차단되지 않았다는 의미**입니다.

---

## nmap

### nmap 옵션 정리

| 옵션 | 설명 | 예시 |
|---|---|---|
| -sS | SYN 스캔 (Stealth Scan) | `nmap -sS 192.168.11.17` |
| -sT | TCP Connect 스캔 | `nmap -sT 192.168.11.17` |
| -sU | UDP 포트 스캔 | `nmap -sU 192.168.11.17` |
| -sV | 서비스 버전 탐지 | `nmap -sV 192.168.11.17` |
| -sN | NULL 스캔 | `nmap -sN 192.168.11.17` |
| -sX | XMAS 스캔 | `nmap -sX 192.168.11.17` |
| -sn | Ping Scan (Host Discovery) | `nmap -sn 192.168.11.0/24` |
| -p | 특정 포트 지정 | `nmap -p 80,443 192.168.11.17` |
| -O | OS 탐지 | `nmap -O 192.168.11.17` |
| -T<0~5> | 스캔 속도 조절 | `nmap -T4 192.168.11.17` |
| -Pn | Ping 차단 환경에서 Host Discovery 생략 | `nmap -Pn 192.168.11.17` |
| -n | DNS 조회 안함 | `nmap -n 192.168.11.17` |
| -v / -vv | 스캔 상세 출력 | `nmap -v 192.168.11.17` |
| -oN | 결과 파일 저장 | `nmap -oN result.txt 192.168.11.17` |
| -iL | IP 목록 파일로 스캔 | `nmap -iL targets.txt` |

### nmap 사용 예시

| 명령어 | 설명 |
|---|---|
| `nmap 192.168.11.17` | TCP 기본 포트 스캔 |
| `nmap -sS 192.168.11.17` | SYN Scan (Stealth Scan) |
| `nmap -sS -sV -O 192.168.11.17` | 열린 포트 + 서비스 버전 + OS 탐지 |
| `nmap -sU 192.168.11.17` | UDP 포트 스캔 |
| `nmap -sU -p 53,161 192.168.11.17` | UDP 53(DNS), 161(SNMP) 탐지 |
| `nmap -sn 192.168.11.0/24` | 네트워크에서 살아있는 Host 탐지 |
| `nmap 192.168.11.1 192.168.11.20` | 여러 대상 스캔 |
| `nmap 192.168.11.1-20` | IP 범위 스캔 |
| `nmap -p 80,443 -sV 192.168.11.17` | 웹 서비스 탐지 |

---

## nmap 실습 (NULL, X-MAS)

### NULL Scan (OPEN port)

- NULL Scan은 **TCP 플래그를 아무것도 설정하지 않은 패킷을 전송하는 스캔 방식**입니다.  
또한 RFC 규칙에 따르면 **열린 포트는 응답하지 않고, 닫힌 포트는 RST를 반환합니다.**

#### 명령어
```
nmap -sN -p 80 192.168.11.17
```

![18](/KH_Security/모의%20해킹/Scan/img/nmap_18.png)
![19](/KH_Security/모의%20해킹/Scan/img/nmap_19.png)

#### 결과
```
PORT   STATE           SERVICE
80/tcp open|filtered   http
```

- 서비스는 http 서버이며, 상태는 열려있거나 필터링 중이라는 뜻이고, 포트는 80번입니다.

#### 패킷 흐름
```
192.168.11.36 → 192.168.11.17   TCP [None]
```

- 공격자는 **TCP 플래그가 없는 패킷(NULL)** 을 전송합니다.
- 서버는 **응답을 하지 않습니다.**
- 따라서 서버에서 응답하지 않았으므로 OPEN Port 또는 Filtered라는 걸 알 수 있습니다.

### NULL Scan (Closed Port)

#### 명령어
```
nmap -sN -p 80 192.168.11.17
```

![20](/KH_Security/모의%20해킹/Scan/img/nmap_20.png)
![21](/KH_Security/모의%20해킹/Scan/img/nmap_21.png)

#### 결과
```
PORT   STATE   SERVICE
80/tcp closed  http
```

- 서비스는 http 서버이며, 상태는 닫혀있다는 뜻이고, 포트는 80번입니다.

#### 패킷 흐름
```
192.168.11.36 → 192.168.11.17   TCP [None]
192.168.11.17 → 192.168.11.36   TCP [RST, ACK]
```

- 포트가 닫혀 있기 때문에 서버는 **RST 패킷을 반환합니다**
- Closed Port라는 걸 알 수 있습니다.

---

### XMAS Scan (OPEN Port)

- XMAS Scan은 **FIN, PSH, URG 플래그를 동시에 설정하여 전송하는 스캔 방식**입니다.   
- 패킷의 플래그가 크리스마스 트리처럼 여러 개 켜져 있다고 해서 **XMAS Scan**이라고 한다.

#### 명령어
```
nmap -sX -p 80 192.168.11.17
```

![22](/KH_Security/모의%20해킹/Scan/img/nmap_22.png)
![23](/KH_Security/모의%20해킹/Scan/img/nmap_23.png)

#### 결과
```
PORT   STATE           SERVICE
80/tcp open|filtered   http
```

#### 패킷 흐름
```
192.168.11.36 → 192.168.11.17   TCP [FIN, PSH, URG]
```

- 공격자는 **FIN + PSH + URG 플래그가 설정된 패킷을 전송합니다**
- 서버는 **응답하지 않습니다**  
따라서, **Open 또는 Filtered**입니다.

### XMAS Scan (Closed Port)

#### 명령어
```
nmap -sX -p 80 192.168.11.17
```

![24](/KH_Security/모의%20해킹/Scan/img/nmap_24.png)
![25](/KH_Security/모의%20해킹/Scan/img/nmap_25.png)

#### 결과
```
PORT   STATE   SERVICE
80/tcp closed  http
```

#### 패킷 흐름
```
192.168.11.36 → 192.168.11.17   TCP [FIN, PSH, URG]
192.168.11.17 → 192.168.11.36   TCP [RST, ACK]
```

- 닫힌 포트는 **비정상 패킷을 받으면 RST를 반환한다**
따라서 `XMAS → RST` 이므로 **Closed Port**인걸 알 수 있습니다.
