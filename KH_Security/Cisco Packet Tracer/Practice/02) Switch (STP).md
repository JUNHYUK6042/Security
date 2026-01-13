# Switch(STP) 및 Root Bridge

---

## STP (Spanning Tree Protocol)

- looping을 제어하는 2계층 프로토콜이고, 다중 경로 환경에서 하나의 논리적 경로만 남기고 나머지 경로는 차단합니다.
- 자동으로 동작하며 네트워크 토폴로지 변화에 따라 경로를 재계산합니다.

### STP의 목적

- Looping 방지
- Broadcast Storm 방지
- MAC Address Table 불안정 방지
- 장애 발생 시 자동으로 대체 경로 활성화

---

## BID (Bridge ID)

- BID는 STP에서 스위치를 식별하기 위한 정보이다.
- STP는 스위치 이름이 아닌 BID 값을 기준으로 Root Bridge를 선출합니다.

### BID 구조

- BID는 총 8Byte(64bit)로 구성됩니다.
  - Bridge Priority (2Byte) + MAC Address (6Byte)

### Bridge Priority

- 기본값은 32768입니다.
- 설정 범위는 `Bridge Priority default : 32768 (0~65535)` 입니다.
- 4096의 배수로만 설정할 수 있습니다.
- 값이 작을수록 우선순위가 높습니다.

---

## BPDU(Bridge Protocol Data Unit)

- 각각의 Switch가 부팅 후 2초마다 BID등의 정보를 교환합니다.
- Path cost & BID 등의 정보를 포함합니다.
- 스위치에 문제가 발생하거나 회선에 문제가 발생시에 새로운 경로를 설정합니다.

---

## Path cost

- 특징
  - 빠를 수록 적은 값을 가진다. Cost값을 기준으로 최적의 경로를 판단합니다.
  - Path Cost는 특정 스위치에서 Root Bridge까지 도달하는 데 필요한 전송 비용을 의미합니다.

---

## STP 구현

### Root Bridge 설정

- 각 스위치들은 BID를 가지고 있습니다.
- BID가 가장 작은 Switch가 Root Bridge가 됩니다.
- 나머지 Switch들은 Non Root Bridge가 됩니다.

### Root Port 결정

- Non Root Bridge에서 Boot Bridge로 가는 경로 중 Path Cost가 가장 낮은 포트가 Root Port로 결정됩니다.
- Path Cost가 같은 경우에는 더 낮은 BID가 Root Port로 선택됩니다.

### Designated Port 결정

- 각 세그먼트에서 Root Bridge까지 가는 Path Cost가 가장 낮은 포트를 Designated Port로 선정합니다.
- Designated Port만 프레임 전송이 허용됩니다.

### Port Block 

- 연결된 링크 중에 root port나 designated port가 아닌 포트는 block 한다.

---

## Packet Tracer 실습

- 4개의 스위치를 서로 연결하여 STP 환경을 구성합니다.
- STP에 의해 차단된 포트는 Packet Tracer에서 주황색으로 표시됩니다.

![18]
