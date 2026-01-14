# VLAN Routing &  Trunk Routing 실습

---

## VLAN Routing 실습

- IP 1.1.1.3을 VLAN10, IP 1.1.1.5를 VLAN 20으로 네트워크를 분리합니다.

![33](/KH_Security/Cisco%20Packet%20Tracer/img/33.png)

---

## Switch와 Router 설정

- Switch

![34](/KH_Security/Cisco%20Packet%20Tracer/img/34.png)

- Router

![35](/KH_Security/Cisco%20Packet%20Tracer/img/35.png)

---

## Swith와 Router 구성 확인

- Switch VLAN 상태 확인
- `show vlan` 명령어를 통해 VLAN 구성을 확인합니다.

![36](/KH_Security/Cisco%20Packet%20Tracer/img/36.png)

- Router Interface 상태 확인
- `show ip int brief` 명령어를 통해 Interface 구성을 확인합니다.

![37](/KH_Security/Cisco%20Packet%20Tracer/img/37.png)

---

## PC1(VLAN 10) 와 PC2(VLAN 20) 통신 테스트

- PC1 (VLAN 10)
  - IP : 1.1.1.3
  - NetMask : 255.255.255.0
  - Gateway : 1.1.1.1
- PC2 (VLAN 20)
  - IP : 2.2.2.3
  - NetMask : 255.255.255.0
  - Gateway : 2.2.2.1

### VLAN 10 -> VLAN 20으로 통신

![38](/KH_Security/Cisco%20Packet%20Tracer/img/38.png)

### VLAN 20 -> VLAN 10으로 통신

![39](/KH_Security/Cisco%20Packet%20Tracer/img/39.png)

---

## Trunk Routing 실습

![40](/KH_Security/Cisco%20Packet%20Tracer/img/40.png)

- Switch와 Router 사이의 링크를 하나로 구성합니다.

---

## Switch Mode Truck 설정 및 Trunk 설정 확인

- S1의 GiabitEthernet 0/1 포트를 Trunk로 설정합니다.

![41](/KH_Security/Cisco%20Packet%20Tracer/img/41.png)

---

## Router 설정 제거 후 인터페이스 확인

### GiabitEthernet 0/0 제거

- GiabitEthernet 0/0의 기존 IP 주소를 제거 후 shutdown 상태로 전환합니다.

![42](/KH_Security/Cisco%20Packet%20Tracer/img/42.png)

- GiabitEthernet 0/1의 기존 IP 주소를 제거 후 shutdown 상태로 전환합니다.

![43](/KH_Security/Cisco%20Packet%20Tracer/img/43.png)

- Router의 인터페이스 상태 확인

![44](/KH_Security/Cisco%20Packet%20Tracer/img/44.png)

---

## Router Sub-Interace 설정

### 물리적 Interface 활성화

![45](/KH_Security/Cisco%20Packet%20Tracer/img/45.png)

---

## VLAN 10 & VLAN 20 Sub-Interface 설정 및 인터페이스 확인

- GigabitEthernet 0/0.10, GiGigabitEthernet 0/0.20 서브인터페이스를 생성합니다.
- encapsulation dot1q 10,20은 "들어오고 나가는 패킷에 IEEE 802.1q 규칙을 써서 VLAN ID = 10, VLAN ID = 20 이라고 구분하겠다"라는 의미입니다.

- VLAN 10 Sub-Interface 설정

![46](/KH_Security/Cisco%20Packet%20Tracer/img/46.png)

- VLAN 20 Sub-Interface 설정

![47](/KH_Security/Cisco%20Packet%20Tracer/img/47.png)

- Interface 확인

![48](/KH_Security/Cisco%20Packet%20Tracer/img/48.png)

---

## VLAN 10과 VLAN 20의 Trunk Routing 통신 테스트

### VLAN 10 -> VLAN 20 통신 테스트

![49](/KH_Security/Cisco%20Packet%20Tracer/img/49.png)

### VLAN 20 -> VLAN 10 통신 테스트

![50](/KH_Security/Cisco%20Packet%20Tracer/img/50.png)

---
