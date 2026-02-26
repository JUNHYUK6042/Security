# TCP

## TCP Overview

### point-to-point
- 단일 송신자와 단일 수신자 간 통신합니다.

### 신뢰적인 in-order byte stream
- 바이트 단위로 순서 보장합니다.
- message 구분이 없습니다. (경계 없음)

### pipelined
- 혼잡제어 및 흐름제어를 통해 window size 조절합니다.
- 여러 segment를 동시에 전송 가능합니다.

### 송·수신측은 buffer를 가짐
- sender buffer
- receiver buffer
- application ↔ TCP는 socket으로 연결합니다.

### full duplex
- 하나의 connection에서 양방향 동시 전송 가능합니다.
- MSS (Maximum Segment Size)
  - segment 안에 담을 수 있는 application data의 최대 크기입니다.

### connection-oriented
- 데이터 전송 전에 handshake 수행합니다.

### Flow control
- receiver가 sender의 전송량을 제어합니다.
- receiver window로 조절합니다.

---

## TCP Segment 구조

```
                   - 32 bits -
  ------------------------------------------------
  |        Source Port        |   Dest Port     |
  ------------------------------------------------
  |                  Sequence Number             |
  ------------------------------------------------
  |               Acknowledgment Number          |
  ------------------------------------------------
  |HLEN|Reserved|U|A|P|R|S|F|   Receive Window  |
  ------------------------------------------------
  |         Checksum           |  Urgent Pointer |
  ------------------------------------------------
  |                Options (variable)            |
  ------------------------------------------------
  |                Application Data              |
  ------------------------------------------------
```

### Source Port (16bit)
- 송신 프로세스 포트 번호입니다.

### Destination Port (16bit)
- 수신 프로세스 포트 번호입니다.

### Sequence Number (32bit)
- 데이터의 **첫 번째 바이트 번호**입니다.
- 바이트 단위로 증가합니다. (segment 단위 아님)

### Acknowledgment Number (32bit)
- 다음에 받을 **첫 번째 바이트 번호**입니다.
- 누적 ACK 방식입니다.

### HLEN (Header Length)
- TCP 헤더 길이입니다. (옵션 포함 시 증가)

### Flags (6bit)

| Flag | 의미 |
|------|------|
| U (URG) | 긴급 데이터 존재 |
| A (ACK) | ACK 번호 유효 |
| P (PSH) | 즉시 상위계층 전달 |
| R (RST) | 연결 리셋 |
| S (SYN) | 연결 설정 |
| F (FIN) | 연결 종료 |

### Receive Window (16bit)
- 수신자가 받을 수 있는 바이트 수입니다.
- Flow Control 핵심 필드입니다.

### Checksum (16bit)
- 오류 검출합니다.
- Internet checksum 방식입니다. (UDP와 동일)

### Urgent Pointer
- 긴급 데이터 위치입니다.
- URG flag와 함께 사용합니다.

### Options
- 가변 길이입니다.
- MSS 등 설정 가능합니다.

### Application Data
- 실제 데이터를 전송합니다. (payload)

---

## TCP seq#와 ACK

![01]()

### Sequence#
- segment의 첫 번째 byte가 stream에서 가지는 번호입니다.

### ACKs
- 다음에 받을 첫 번째 byte의 순서 번호입니다.
- cumulative ACK 가능합니다.

#### 순서가 틀린 segment를 받은 경우

- RFC에 명확한 규칙은 없습니다.

- 기본적으로 2가지 방법
  - 즉시 버립니다.
  - buffering 합니다. (중간에 빠진 데이터가 올 때까지 저장)
 
---

## RTT (Round Trip Time) & Timeout

### Timeout 설정 원칙
- RTT보다 커야 한다.
- 너무 작으면 → 불필요한 재전송 발생
- 너무 크면 → 손실 대응이 느려짐

---

### SampleRTT
- segment가 송신된 시점부터 ACK가 도착할 때까지의 시간
- 재전송된 segment는 제외

---

### EstimatedRTT
- 현재 네트워크 평균 왕복 시간의 추세값

- **필요한 이유 :**
```text
RTT는 매번 달라진다.
SampleRTT 하나만 사용하면 재전송 타이머가 계속 흔들린다.
그래서 최근 RTT들의 평균적인 경향을 추정할 값이 필요하다.
```

- **계산식 :**
```text
EstimatedRTT = (1 - α) * EstimatedRTT + α * SampleRTT
```

- 역할 :
```text
재전송 타이머의 기본 기준이 된다.
현재 네트워크 속도를 대표하는 값이다.
```

- Exponential Weighted Moving Average 방식
- 과거 값의 영향은 점점 감소
- α의 대표값 : 0.125

---

### DevRTT

- 의미 :
```text
RTT의 변동성(흔들림 정도)
```

- 필요한 이유 :
```text
평균값만으로는 네트워크의 불안정성을 알 수 없습니다.
RTT가 크게 튀는 상황에서도 안전하게 동작하려면 변동성을 함께 고려해야 합니다.
```

- 계산식 :
```text
DevRTT = (1 - β) × 이전 DevRTT + β × |SampleRTT - EstimatedRTT|
```

- 왜 절댓값 차이를 사용하는가 :
```text
평균에서 얼마나 벗어났는지를 측정하기 위함입니다.
차이가 크면 네트워크가 불안정하다는 의미입니다.
```

- 역할 :
```text
Timeout 설정 시 안전 여유값을 제공합니다.
네트워크가 불안정할수록 Timeout을 더 크게 만듭니다.
```

- SampleRTT와 EstimatedRTT의 차이 예측
- DevRTT = (1 - β) * DevRTT + β * |SampleRTT - EstimatedRTT|
- β 권장값 : 0.25

---

### 실제 Timeout 설정

```text
TimeoutInterval = EstimatedRTT + 4 * DevRTT
```
