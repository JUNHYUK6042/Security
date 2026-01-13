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
