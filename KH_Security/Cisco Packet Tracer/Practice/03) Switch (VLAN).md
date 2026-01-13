# Switch VLAN 실습

---

## Switch의 기능

| 기능 | 설명 |
|---|---|
| Learning | 수신 프레임의 출발지 MAC 주소를 MAC 테이블에 학습 |
| Filtering | 목적지 MAC이 동일 포트일 경우 프레임을 차단 |
| Forwarding | 목적지 MAC이 다른 포트에 존재하면 해당 포트로 전달 |
| Flooding | 목적지 MAC을 모를 경우 모든 포트로 전송 |

---

## VLAN (Virtual LAN)

- 스위치 내부를 여러 개의 독립 스위치 장비처럼 분할해 주거나 이웃 스위치와 통합하는 기능을 제공한다.
- VLAN이 다르면 동일한 IP 대역이어도 통신할 수 없습니다.
- VLAN 간 통신을 위해서는 라우터 또는 L3 장비가 필요합니다.

---

## VLAN 명령어

| 명령어 | 설명 |
| --- | --- |
| show vlan | VLAN 확인 |
| show interface status | 각 포트의 인터페이스 상태 확인 |
| vlan database | VLAN 모드 진입 - LAN 생성 |
| vlan ## | VLAN 선택 또는 생성 |
| name [vlan name] | VLAN 이름 지정 |
| switchport access vlan ## | 인터페이스를 VLAN에 할당 |
| switchport mode access | 단일 VLAN 사용 |
| switchport mode trunk | 여러 VLAN 트래픽 전달 모드 |
| switchport mode dynamic auto | 상대에 따라 달라짐 |
| switchport mode dynamic desirable | 트렁크 협상 시도 (가급적 사용 안함) |

---

## VLAN 실습

![20](/KH_Security/Cisco%20Packet%20Tracer/img/20.png)

### VLAN 2
- 1.1.1.3, 1.1.1.5
- FastEthernet 0/1, 0/2

### VLAN 3
- 1.1.1.7, 1.1.1.9
- FastEthernet 0/1, 0/2

---

## Switch의 VLAN 초기 상태 확인

![21](/KH_Security/Cisco%20Packet%20Tracer/img/21.png)

- 모든 포트가 VLAN 1에 있는 것을 확인할 수 있습니다.

---

## VLAN 생성 및 인터페이스 할당

- VLAN 2 생성 (VLAN 3도 똑같은 방법을 하면 된다.)

![22](/KH_Security/Cisco%20Packet%20Tracer/img/22.png)

- VLAN 2 : FastEthernet 0/1, FastEthernet 0/2 포트를 할당합니다.
- int range fa0/1-2 여러 포트를 할당할 때 사용하면 편리하다.

![23](/KH_Security/Cisco%20Packet%20Tracer/img/23.png)


- VLAN 3 : FastEthernet 0/3, FastEthernet 0/4 포트를 할당합니다.
- int range fa0/3-4 여러 포트를 할당할 때 사용하면 편리하다.

![24](/KH_Security/Cisco%20Packet%20Tracer/img/24.png)

---

## PC1(VLAN 2)와 PC2(VLAN 3)의 통신

- PC2(VLAN 3) -> PC1(VLAN 2) 통신 테스트를 해봅니다.

![25](/KH_Security/Cisco%20Packet%20Tracer/img/25.png)

- PC1(VLAN 2) -> PC2(VLAN 3) 통신 테스트를 해봅니다.

![26](/KH_Security/Cisco%20Packet%20Tracer/img/26.png)

---

## VLAN - Trunk 실습 

- Switch 간 Gig0/1 포트를 Trunk로 정의하여 모든 VLAN 패킷을 전달하는 실습을 하였습니다.

![29](/KH_Security/Cisco%20Packet%20Tracer/img/29.png)

- 각각의 스위치에 VLAN 생성하고 인터페이스 할당할 때 Trunk로 설정한 과정입니다.
![27](/KH_Security/Cisco%20Packet%20Tracer/img/27.png)
![28](/KH_Security/Cisco%20Packet%20Tracer/img/28.png)

---

## 같은 VLAN에 있는 PC간 통신 테스트

- VLAN 1 PC에서의 통신

![30](/KH_Security/Cisco%20Packet%20Tracer/img/30.png)

- VLAN 10 PC에서의 통신

![31](/KH_Security/Cisco%20Packet%20Tracer/img/31.png)

- VLAN 10 PC에서의 통신

![32](/KH_Security/Cisco%20Packet%20Tracer/img/32.png)

---
