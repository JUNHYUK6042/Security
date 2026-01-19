# Router - OSPF 

---

## 개요

OSPF(Open Shortest Path First)는 링크 상태(Link-State) 기반의 IGP(Interior Gateway Protocol) 로,  
하나의 AS(Autonomous System) 내부에서 사용되는 동적 라우팅 프로토콜이다.

- 계층적(Hierarchical) 구조 지원

- Area 단위로 라우팅 정보 관리
  - Backbone area(Area0)을 통해 연결된다.
  - Area0에 연결되지 못하면 Virtual link를 통해 연결한다.
  - Area 내에서 LSA를 교환한다.
 
- Link의 cost를 기반으로 경로를 배정한다.
  - Hop 제한이 없다.
  - Dijkstra의 SPF알고리즘을 바탕으로 경로가 선택된다.
  - link cost = 기준대역폭/실제대역폭

- VLSM 및 CIDR 지원

---

## OSPF - 구조

### 라우터 구분

- `IR` : Area 내부 라우터
- `ABR` : Area를 연결하는 라우터
- `ASBR` : AS와 연결하는 외부 연결 라우터
- `DR(Designated Router)`
  - Link stat 정보를 취합, 관리하는 라우터
  - IR과 DR간에 link stat 정보를 주고 받는다.
- BDR(Backup DR)

- OSPF는 neighbor 간에 라우팅 정보를 공유한다.
  - 라우터 간에 인접관계가 있어야만 한다.
  - 동일한 area에 위치한다.
  - 동일한 인증 정보

---

## OSPF - Neighbor, Adjacency

- 네이버(Neighbor)와 인접 관계(Adjacency)는 동일한 의미가 아닙니다.
- 인접관계는 DR(BDR)과 내부 라우터간에 통신이 이루어 줘야 합니다.

![Neighbor.Adjacency](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/Neighbor_Adjacency.png.jpg)

### Point to Poing Link  
- HDLC, PPP등의 Serial Link
- DB/BDR를 선출 하지 않는다.
- OSPF Hello 및 LSU 패킷은 Multicast 224.0.0.5를 이용한다.
  - Hello Packet : 10s
  - Dead Interval : 40s
 
---

## OSPF - Multiaccess Network

- Ethernet등의 LAN Link
- DR/BDR 선출
  - Priority가 기준이 되며, 가장 높은 Router Id가 DR이 됩니다.
- 모든 Router는 DR & BDR과만 인접 관계가 형성됩니다.

![OSPF/MA](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/OSPF_MA.png)

- 위의 그림처럼 DR/BDR이 선출이 되면 IR과 통신이 이뤄지기 때문에 인접 관계가 형성된다.

---

## OSPF - Metric

- 각 프로토콜 metric
| Protocol | Metric 기준 |
| --- | --- |
| RIP | Hop Count |
| OSPF | Bandwidth, Delay 등 |
| EIGRP | Cost(대역폭) |

- OSPF 선택경로 : 전체 코스트의 합이 가장 낮은 경로를 선택합니다.

---

## OSPF - SPF 실습

- 가장 먼저 각 라우터의 테이블을 만듭니다.
- 트리를 그려줍니다.
- 경로를 계산하여 마지막으로 각 라우터의 라우팅 테이블을 만들어줍니다.

![SPF](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/SPF.png)

---

## OSPF 구성 실습 - Serial

![01](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/01.png)

### OSPF 프로세스 활성화

- 명령 : `router ospf 1`

#### R1 세팅 & 인터페이스 상태 확인

- 다음과 같이 필수로 해야하는 기본적인 라우터 설정을 해줍니다.

![02](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/02.png)

- R1에 연결된 Serial 라인에 대한 인터페이스 설정을 합니다.
- 인터페이스 상태를 확인합니다.

![03](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/03.png)
![04](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/04.png)

#### R2 세팅 & 인터페이스 상태 확인

![05](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/05.png)
![06](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/06.png)

#### R3 세팅 & 인터페이스 상태 확인

![07](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/07.png)
![08](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/08.png)

---

## 각 PC들의 IP & Gateway & NetMask 설정

- PC1

![09](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/09.png)

- PC2

![10](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/10.png)

- PC3

![11](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/11.png)

- 각각 PC의 네트워크를 설정해 줍니다.

---

## OSPF - Interface 설정

### R1

- `router ospf 1` 명령어를 통해 인터페이스 설정합니다.

![12](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/12.png)

- `show ip route ospf`

![13](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/16.png)

- `show ip ospf int s0/0/0`

![14](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/17.png)

- `show ip ip ospf neighbor`

![15](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/18.png)

---

### R2

- `router ospf 1` 명령어를 통해 인터페이스 설정합니다.

![16](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/13.png)

- `show ip route ospf`

![17](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/19.png)

- `show ip ospf int s0/0/0`

![18](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/20.png)

- `show ip ip ospf neighbor`

![19](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/21.png)

---

### R3 

- `router ospf 1` 명령어를 통해 인터페이스 설정합니다.

![20](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/14.png)

- `show ip route ospf`

![21](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/22.png)

- `show ip ospf int s0/0/0`

![22](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/23.png)

- `show ip ospf neighbor`

![23](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF%20img/24.png)

---
