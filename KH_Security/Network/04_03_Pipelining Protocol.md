
# Pipelining Protocol(GBN, Selective Repeat)

## 개요

- sender에게 ACK를 기다리지 않고 여러 개의 pkt를 전송하도록 허용하는 것입니다.

### 등장 배경
- rdt 3.0 (stop-and-wait)는 한 번에 하나의 패킷만 전송 → RTT 동안 링크 낭비 발생
  - RTT : 패킷을 보내고 그에 대한 ACK를 받기까지 걸리는 왕복 시간

- 대역폭을 효율적으로 사용하기 위해 등장


### 특징
- sequence number 범위 증가 필요
- sender와 receiver 모두 버퍼링 필요
- window 개념 사용

#### 예시

```text
Stop-and-Wait :
[SEND 1] → ACK 기다림 → [SEND 2]

Pipelining :
[SEND 1][SEND 2][SEND 3][SEND 4] → ACK 순차 처리
```

---

## Go-Back-N (GBN)

- 오류가 발생하면 해당 패킷 이후의 모든 패킷을 다시 전송하는 누적 ACK 기반 프로토콜

### Sender
```text
- Sender는 window 범위 내에서 패킷을 전송하고, timeout이 발생하면 window 내 모든 패킷을 재전송한다.
- k-bit sequence number 사용합니다.
- window(전송되었지만 확인안된 pkt를 위해 허용 할수있는 seq #의 범위) 사용합니다.
- 단일 timer (가장 오래된 unACKed packet 기준)
- ACK(n) : seq # n을 가진 ACK를 `cumulative ACK`로 인식합니다.
  → 수신측에서 보면 n을 포함한 n까지의 모든 pkt에 대한 ACK입니다.
```

### GBN : sender FSM
- base: 아직 ACK받지 못한 가장 오래된 패킷 번호
- nextseqnum: 다음에 보낼 패킷 번호
- N: window size

![01]()

- **rdt_send(data)**
```text
- if(nextseqnum < base + N) -> window 범위 안이면 전송 가능
  - 다음에 보낼 패킷 번호, data, checksum을 붙여서 패킷을 생성 후 하위 계층으로 패킷 전송

- base == nextseqnum
  - 타이머 시작

- nextseqnum++ : 다음에 보낼 패킷 번호 증가

- refuse_data(data) : window 범위 초과 시 데이터 거부
```

- **rdt_rcv(rcvpkt) && notcorrupt(rcvpkt)**
```text
- base = getacknum(rcvpkt) + 1 : 정상 수신된 패킷에 1을 증가

- if (base == nextseqnum) stop_timer
  - 확인 안 된 패킷이 없기때문에 타이머 중지
- else start_timer
  - 그게 아닐시 타이머를 시작합니다.
```

- **Time out**
```text
start_timer
udt_send(sndpkt[base])
udt_send(sndpkt[base+1])
...
udt_send(sndpkt[nextseqnum-1])
- base부터 window 끝까지 전부 재전송
```

---

### GBN : sender FSM

- ACK만 사용 : 항상 현재까지 수신된 가장 높은 seq#를 가진 패킷에 대한 ACK를 전송합니다.
  - 중복된 ACK가 발생 : 와야될 패킷이 오지 않고 다른 번호를 가진 패킷이 올 경우에 해당됩니다.
  - 단지 expectedseqnum만을 유지 : 현재까지 수신된 패킷 seq번호의 다음 seq번호를 가집니다.

- 순서가 잘못 수신된 pkt의 처리
  - 순서가 잘못된 패킷에 대해서 buffering 할 필요없이 버립니다.
  - 순서가 잘못된 패킷이 오면 현재까지 수신된 가장 큰 seq 번호에 대한 ACK를 재전송합니다.

![02]()

- **A**
```text
- expectedseqnum = 1 : 다음에 받아야 할 패킷 번호

- sndpkt = make_pkt(expectedseqnum, ACK, checksum) :
  - 
```

- **rdt_rcv(rcvpkt) && notcorrupt(rcvpkt) && hasseqnum(rcvpkt, expectedseqnum)**
```text
- extract(rcvpkt, data) : 데이터를 추출

- deliver_data(data) : 추출한 데이터를 상위 계층으로 전달

- sndpkt = make_pkt(expectedseqnum, ACK, checksum) :
  - 다음에 받아야할 패킷번호, ACK, checksum을 붙여 패킷 생성

- udt_send(sndpkt) : ACK 패킷 전송

- expectedseqnum++ : 다음에 받을 패킷 번호 증가
```

- in-order : 정상 처리합니다.
- out-order : 패킷을 버립니다.
- 이외의 경우 무시합니다.

---

## Selective Repeat (SR) Protocol 정리

- 개별 ACK와 수신 버퍼링을 사용하여 손실된 패킷만 재전송하는 효율적인 신뢰성 프로토콜입니다.

### Sender

#### 전송 조건

- `다음 seq가 sender window 안에 있으면 전송`

#### ACK(n) 수신 시

- 해당 패킷을 확인 완료로 표시합니다.
- `n == send_base :`
  - send_base를 가장 작은 미확인 seq#로 이동합니다.
  - window내에 패킷을 전송합니다.

#### timeout(n) 발생 시

- 해당 pkt n만 재전송하고 그 패킷의 timer만 restart합니다.

#### GBN과 차이:

- GBN은 window 전체 재전송  
- SR은 **해당 패킷만 재전송**

---

### Receiver

- **pkt n [n in rcvbase, rcvbase+N-1]**
```text
- 수신한 pkt마다 개별 ACK(n) 전송합니다.
- out-of-order 패킷은 버퍼에 저장합니다.
- in-order 패킷이 도착하면:
  - 상위 계층에 전달 후 연속적으로 저장된 패킷도 함께 전달합니다.
  - rcv_base를 가장 낮은 seq#를 가진 미전송 pkt로 옮깁니다.
```

- **pkt n [n in rcvbase-N, rcvbase-1]**
```text
- ACK(n) 재전송
- 이외의 경우에는 buffer에 저장하지 않고, 데이터를 다시 전달하지 않습니다.
```
---

### Selective repeat의 문제
 
- Selective Repeat은 window size가 sequence number 공간의 절반을 초과하면  
이전 패킷과 새로운 패킷을 구별할 수 없는 심각한 문제가 발생한다.

#### 문제 발생 과정

EX)
- Seq# : 0, 1, 2, 3, 4
- windows size =2

```text
- Sender가 0, 1 전송
  - Receiver는 정상 수신 후 ACK 전송  
    하지만 ACK가 손실되었다고 가정, Sender는 timeout 후 0을 재전송합니다
- 그 사이 Receiver window가 이동한 경우
  - Receiver는 이미 0, 1 수신 완료하여 window가 앞으로 이동합니다.
  - 이제 새로운 0이 들어올 수 있는 상태가 된다.
```

- **문제 발생**
```text
- Receiver는 판단 불가 :
  - 이 0이 과거 패킷인가? 아니면 새로 순환된 seq# 0인지 구별할 수 없습니다.
```

- **문제가 생기는 이유 :**
```text
Sequence number는 유한한 공간을 가지는데, 순환(modulo) 구조이기 때문에  
window가 너무 크면 과거 범위와 미래 범위가 겹치게 됩니다.
```

- **해결 조건**
```text
window size ≤ sequence number 공간의 절반
```

---

## SR과 GBN 비교

| 항목 | GBN | SR |
|------|------|------|
| ACK 방식 | 누적 ACK | 개별 ACK |
| Out-of-order 처리 | 버림 | 버퍼링 |
| 재전송 방식 | 전체 재전송 | 선택적 재전송 |
| 타이머 | 1개 | 패킷별 |
| 효율성 | 낮음 | 높음 |
| 구현 난이도 | 낮음 | 높음 |

---

## 정리
- `Go-Back-N Sender는 "window 기반 전송 + timeout 시 전체 재전송" 구조이다.`  
- `Go-Back-N Receiver는 "정상 순서 패킷만 처리하고, 나머지는 무시하는 단순 누적 ACK 구조"이다.`
- Selective Repeat의 문제점은 "sequence number가 순환하는 구조에서 window가 너무 크면  
과거 패킷과 새로운 패킷을 구별할 수 없다는 것"이다.
