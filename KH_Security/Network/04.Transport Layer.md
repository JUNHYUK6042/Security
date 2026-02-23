# Transport Layer

## 개요

- 본 문서는 Transport Layer에서 제공하는 서비스와 TCP/UDP의 동작 원리를 정리한 학습 문서입니다.
- 다중화/역다중화, 신뢰적 데이터 전송, 흐름 제어, 혼잡 제어의 핵심 개념을 이해합니다.
- 인터넷 Transport Layer는 TCP와 UDP 두 가지 프로토콜을 사용합니다.

---

# 4.1 Transport Layer 서비스

## Transport Layer 역할

- 서로 다른 host 간 application process 간의 논리적 통신(logical communication)을 제공합니다.
- Transport protocol은 end system에서 동작합니다.

#### 송신측 동작
- application message를 segment로 변환
- network layer에 전달

### 수신측 동작
- network layer로부터 segment 수신
- message를 추출하여 application layer에 전달

- 하나의 네트워크 application은 하나 이상의 transport protocol을 사용할 수 있습니다.
  - 인터넷: TCP, UDP

---

## Transport Layer vs Network Layer

- Network Layer: host 간 logical communication 제공
- Transport Layer: process 간 logical communication 제공

### 특징

- Transport Layer는 Network Layer가 제공하지 못하는 신뢰적인 전송을 제공할 수 있습니다.
- 하지만 다음은 보장하지 않습니다.
  - 최대 지연 시간
  - 전달 대역폭

---

## 인터넷 Transport Layer 개요

### TCP (신뢰적 연결지향 서비스)

- 혼잡 제어
- 흐름 제어
- 연결지향
- 신뢰적 데이터 전송

### UDP (비신뢰적 비연결지향 서비스)

- best-effort delivery service (IP 기반)
- 손실 가능
- 순서 보장 없음
- 혼잡 제어 없음
- 연결 설정 과정 없음

---

# 4.2 다중화와 역다중화

## Multiplexing / Demultiplexing

### Multiplexing (송신측)

- 여러 socket으로부터 data를 모음
- 각 data에 header 정보를 추가하여 segment로 캡슐화
- 하위 layer로 전달

### Demultiplexing (수신측)

- 수신된 segment의 목적지 port 번호를 확인
- 해당 socket으로 data 전달

---

## Demultiplexing 요구 사항

- 각 socket은 유일한 식별자를 가져야 한다.
- 각 segment는 적절한 socket을 가리키는 특별한 field를 가져야 한다.
- 이 field는 source port 번호와 destination port 번호이다.

### Port 번호

- 범위: 0 ~ 65535
- 0 ~ 1023: well-known port (RFC 1700 명시)

### 동작 방식

- segment가 host에 도착
- Transport Layer가 destination port 번호 검사
- 해당 socket으로 전달

---

## Connectionless Demultiplexing (UDP)

- UDP socket은 (destination IP, destination port)로 식별된다.
- source IP나 source port가 달라도 destination이 같으면 동일 socket으로 전달된다.

---

## Connection-Oriented Demultiplexing (TCP)

- TCP socket은 다음 4개 요소로 식별된다.
  - source IP
  - source port
  - destination IP
  - destination port

- Server는 동시에 여러 TCP socket을 지원할 수 있다.
- 각 connection은 서로 다른 4-tuple로 구별된다.
- non-persistent HTTP의 경우 요청마다 새로운 TCP connection을 사용한다.

---

# 4.3 UDP

## UDP 특징 (RFC 768)

- 최소 기능의 Transport protocol
- multiplexing / demultiplexing 기능만 제공
- connectionless
- best-effort 방식

### 보장하지 않는 것

- 손실 없음 보장 X
- 순서 보장 X
- 혼잡 제어 없음

---

## UDP를 사용하는 이유

- 연결 설정이 없다 (지연 없음)
- 연결 상태 유지가 필요 없다
- header 크기 작음 (8 byte, TCP는 20 byte)
- 혼잡 제어 없음

### 사용 예

- DNS
- SNMP
- 스트리밍 멀티미디어 (손실 허용, rate 민감)

---

## UDP Checksum

### 목적

- 전송 중 오류 검출

### Sender 동작

1. segment 내용을 16bit 단위로 나눈다.
2. 모든 16bit 값을 더한다.
3. 1의 보수를 수행한다.
4. 결과를 checksum field에 삽입한다.

### Receiver 동작

1. checksum 포함 모든 16bit 값을 더한다.
2. 결과가 모두 1이면 정상
3. 0이 있으면 오류
