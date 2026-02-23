# TCP

## TCP Overview

### point-to-point
- 단일 송신자와 단일 수신자 간 통신

### 신뢰적인 in-order byte stream
- 바이트 단위로 순서 보장
- message 구분이 없음 (경계 없음)

### pipelined
- 혼잡제어 및 흐름제어를 통해 window size 조절
- 여러 segment를 동시에 전송 가능

### 송·수신측은 buffer를 가짐
- sender buffer
- receiver buffer
- application ↔ TCP는 socket으로 연결

### full duplex
- 하나의 connection에서 양방향 동시 전송 가능
- MSS (Maximum Segment Size)
  - segment 안에 담을 수 있는 application data의 최대 크기

### connection-oriented
- 데이터 전송 전에 handshake 수행

### Flow control
- receiver가 sender의 전송량을 제어
- receiver window로 조절

---

## TCP Segment 구조

