# Packet Tracer 기초 & CLI 모드

- Cisco Packet Tracer 환경에서 기본 네트워크 토폴로지 구성과 CLI 모드 진입 과정을 정리한 실습 문서입니다.
- PC 2대, Switch 2960, Router 2911을 이용하여 기본 통신 확인 및 CLI 모드 구조를 학습합니다.

---

## 네트워크 구성

- PC 2대, Switch 2960, Router 2911 장비를 사용하여 실습해보았습니다.

---

## 라우터와 스위치 연결

- `Copper Straight-Through(직선 케이블)`를 사용하여 다음과 같이 연결합니다.
  - Router : GigabitEthernet0/0
  - Switch : GigabitEthernet0/1
- 라우터 인터페이스는 0/0부터 시작하며, 스위치는 0/1부터 시작합니다.

![01](/KH_Security/Cisco%20Packet%20Tracer/img/01.png)
![02](/KH_Security/Cisco%20Packet%20Tracer/img/02.png)

---

## 스위치와 PC 2대 연결

- Switch FastEthernet0/1, FastEthernet0/2 포트와 PC의 FastEthernet0 포트를 연결합니다.

![03](/KH_Security/Cisco%20Packet%20Tracer/img/03.png)
![04](/KH_Security/Cisco%20Packet%20Tracer/img/04.png)

---

## IP 주소 설정 PC1(1.1.1.3/24)

- PC(1.1.1.3)에는 다음과 같이 할당 해줍니다.

  ![05](/KH_Security/Cisco%20Packet%20Tracer/img/05.png)

---

## IP 주소 설정 PC2(1.1.1.5/24)

- PC(1.1.1.5)에는 다음과 같이 할당 해줍니다.

![06](/KH_Security/Cisco%20Packet%20Tracer/img/06.png)

---

## IP 주소 확인

- PC에서 ipconfig 명령어를 통해 IP 주소 설정을 확인합니다.

![07](/KH_Security/Cisco%20Packet%20Tracer/img/07.png)
![08](/KH_Security/Cisco%20Packet%20Tracer/img/08.png)

---

## PC간 통신

- PC1(1.1.1.3)에서 PC2(1.1.1.5)로 ping 명령어로 통신 테스트를 수행합니다.

![09](/KH_Security/Cisco%20Packet%20Tracer/img/09.png)

---

## CLI 모드

- User EXEC Mode, Privileged EXEC Mode, Global Configuration Mode 등 3가지 모드로 구성되어 있습니다.

---

## User EXEC Mode (사용자 모드)

- 표시 기호 : `>`
- 용도 : 기본 명령어 실행, 상태 확인
- 장치 설정 불가 

---

## Privilged EXEC Mode (관리자 모드)

- 표시 기호 : `#`
- 용도 : 전체 명령어 사용, 로그 확인, 설정 모드 진입
- 접근 방법 : User EXEC Mode에서 enable 입력

---

## Global Confituration Mode (전역 설정 모드)

- 표시 기호 : `(config)#`
- 용도 : 장비 전체 설정 변경
- 접근 방법 : Privilged EXEC Mode에서 configure terminal 입력
- 단축 명령어 : conf t

---

## CLI - User EXEC Mode 확인

- CLI에 접속시 기본적으로 User EXEC Mode로 시작한다.

![10](/KH_Security/Cisco%20Packet%20Tracer/img/10.png)

---

## CLI - Privileged EXEC Mode 진입

- User EXEC Mode에서 `enable`명령어를 입력하여 진입합니다.

![11](/KH_Security/Cisco%20Packet%20Tracer/img/11.png)

- Privileged EXEC Mode에서 User EXEC Mode로 돌아가기 위해서는 `disable` 명령어를 입력해 줍니다.

---

## CLI - Global Configuration Mode 진입

- Privileged EXEC Mode에서 `configure terminal(conf t)명령어를 입력하여 진입합니다.

![12](/KH_Security/Cisco%20Packet%20Tracer/img/12.png)

- Global Configuration Mode에서 Privileged EXEC Mode로 돌아가기 위해서는 `exit` 모드를 입력해 줍니다.

---

## CLI - 라우터 및 스위치 설정시 필수 명령어

- hostname 설정
- no ip domain-lookup (DNS lookup 기능 해제)
- exit (Global Configuration Mode에서 Privileged EXEC Mode로 이동)
- copy running-config startup-config (NVRAM에 설정 저장 명령어)

![13](/KH_Security/Cisco%20Packet%20Tracer/img/13.png)
