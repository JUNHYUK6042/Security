# NIC 추가 및 설정
- 가상머신에 랜카드 추가 후 XWindow와 nmtui 명령어를 통해 IP 주소를 설정하는 과정

---

## NIC 설정 서비스 확인
- 설정 전에 NetworkManager를 통해 서비스를 확인합니다.
- `systemctl is-active NetworkManager.service`
- `systemctl is-enabled NetworkManager.service`의 명령어를 통해 서비스를 확인합니다.
-  현재 활동 - 프로그램 표시 - 설정 - 네트워크 메뉴에서 설정
-  변경한 내용은 즉시 적용됩니다.

---

## 설정 적용 방식

### 시스템 재시작에 의한 적용

- 설정 파일은 시스템 재시작 시 새로 적용됩니다.

### 명령어에 의한 적용

- `systemctl restart NetworkManager.service`
  - IP가 추가됨
  - 기존 IP를 보존하여 기존 접속 유지

- `nmcli conn up [NIC]`
- `nmcli dev disconnect [NIC] && nmcli dev connect [NIC]`
  - IP가 변경됨
  - 원격 접속 중일 경우 터미널 연결이 끊길 수 있음

---

## NIC 추가 (Xwindow에서의 네트워크 설정)

- VMware 화면에서 설정 버튼 누르고 Network Adapter(NAT방식)을 추가합니다.
- 설정 완료 후 VMware 화면에서 Network Adapter 2가 추가된 것을 확인할 수 있습니다.

![01](/KH_Security/Linux/Network%20Setting%20-%201/img/01.png)

- Rocky Linux 부팅 후 현재 활동 -> 프로그램 표시 -> 설정 -> 네트워크 메뉴로 이동합니다.

![02](/KH_Security/Linux/Network%20Setting%20-%201/img/02.png)

---

### NIC 확인

- Ethernet 항목에서 다음 두 인터페이스를 확인합니다.
  - 기존 인터페이스: `ens160`
  - 새로 추가된 인터페이스: `ens224`

---

### NIC 신원 설정 및 IPv4 설정

- NIC 신원 설정

![03](/KH_Security/Linux/Network%20Setting%20-%201/img/03.png)

- NIC IPv4 설정

![04](/KH_Security/Linux/Network%20Setting%20-%201/img/04.png)

- IP 수동 설정
- IPv4 : 192.168.11.###
- NetMask : 255.255.255.0

---

## NIC 추가 (nmtui 명령어를 통한 네트워크 설정)

- nmtui 명령어 입력하면 다음과 같이 화면이 나옵니다.

![05](/KH_Security/Linux/Network%20Setting%20-%201/img/05.png)

- 연결 편집으로 들어갑니다.

![06](/KH_Security/Linux/Network%20Setting%20-%201/img/06.png)

- 추가 -> 이더넷 -> 생성을 순서대로 눌러주면 다음과 같이 화면이 나옵니다.

![07](/KH_Security/Linux/Network%20Setting%20-%201/img/07.png)

- IPv4에서 자동 -> 수동
- 숨기기 -> 보이기
- IP : 192.168.11.136/24로 설정

- 설정 완료 시 확인 버튼을 눌러줍니다.

![08](/KH_Security/Linux/Network%20Setting%20-%201/img/08.png)

---

## 설정 확인

- `ip a` 명령어를 통해 잘 추가가 되었는지 확인해 줍니다.

![09](/KH_Security/Linux/Network%20Setting%20-%201/img/09.png)

---

## 인터페이스 활성화 및 비활성화

- 명령어
  - `ip link set [NIC] up / down`
- 지정한 인터페이스를 활성화하거나 비활성화 한다.
- 이를 통해서는 IP 등의 설정을 변경 할 수 없다.
  - 항구적인 설정 변경은 불가하지만 임시 설정을 복원할 수 있다.
