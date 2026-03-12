# ARP Spoofing

## 개요
ARP(Address Resolution Protocol)는 IP 주소를 기반으로 해당 장치의 MAC 주소를 알아내기 위한 프로토콜이다.  
ARP Spoofing은 위조된 ARP Reply 패킷을 전송하여 피해자의 ARP 테이블을 조작하고 공격자의 MAC 주소로 트래픽을 보내도록 만드는 공격 기법이다.

---

## 실습 도구 설치

```
apt install -y fake
```

![01](/KH_Security/모의%20해킹/ARP%20Spoofing/img/01.png)

- 다음과 같이 fake 도구를 설치 해줍니다.

---

## ARP Spoofing 실습

### 실습 목적
send_arp 프로그램을 이용하여 ARP 패킷을 위조하고  
피해자의 ARP 테이블이 변경되는 과정과 Wireshark에서 ARP 패킷을 분석한다.

---

## 실습 환경

| 구분 | IP | MAC |
|---|---|---|
| Windows | 192.168.11.7 | 00:0c:29:f9:78:42 | 
| Linux | 192.168.11.17 | 00:0c:29:a4:4a:38 |
| 공격자 (Kali) | 192.168.11.36 | 00:0c:29:8e:f5:9c |

---

## 공격 전 ARP 테이블 확인

### Windows (192.168.11.7)

```bash
arp -a
```
![02](/KH_Security/모의%20해킹/ARP%20Spoofing/img/02.png)

- 위와 같이 정상적으로 IP와 MAC 주소가 매핑되어 있는지 확인해줍니다.

### Linux (192.168.11.17)

```
ip n
```
![03](/KH_Security/모의%20해킹/ARP%20Spoofing/img/03.png)

- 위와 같이 정상적으로 IP와 MAC 주소가 매핑되어 있는지 확인해줍니다.

---

## ARP Spoofing 공격

- 다음과 같은 명령어를 통해 ARP Spoofing 공격을 시도합니다.
```
send_arp 192.168.11.17 00:0c:29:8e:f5:9c 192.168.11.7 00:0c:29:f9:78:42
-> 192.168.11.7에게 192.168.11.17의 MAC 주소를 공격자의 MAC 주소(00:0c:29:8e:f5:9c)라고 알린다.
```
```
send_arp 192.168.11.7 00:0c:29:8e:f5:9c 192.168.11.17 00:0c:29:a4:4a:38
-> 192.168.11.17에게 192.168.11.7의 MAC 주소를 공격자의 MAC 주소(00:0c:29:8e:f5:9c)라고 알린다.
```

![04](/KH_Security/모의%20해킹/ARP%20Spoofing/img/04.png)

---

## 공격 후 ARP 테이블 확인

### Windows

![05](/KH_Security/모의%20해킹/ARP%20Spoofing/img/05.png)

-  두 IP 주소가 동일한 MAC 주소로 변경된 것을 확인할 수 있는데,  
이는 ARP Spoofing 공격으로 인해 ARP 테이블이 조작된 상태입니다.

### Linux

![06](/KH_Security/모의%20해킹/ARP%20Spoofing/img/06.png)

- Linux도 마찬가지로 두 IP 주소가 동일한 MAC 주소로 변경된 것을 확인할 수 있는데,  
이는 ARP Spoofing 공격으로 인해 ARP 테이블이 조작된 상태입니다.

---

## Wireshark 패킷 분석

### ARP Request

![07](/KH_Security/모의%20해킹/ARP%20Spoofing/img/08.png)

### ARP Reply

![08](/KH_Security/모의%20해킹/ARP%20Spoofing/img/08.png)
