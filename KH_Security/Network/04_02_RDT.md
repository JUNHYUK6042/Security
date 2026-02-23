# TCP_UDP (Transport Layer)

## 신뢰적 Data transfer

![01]()

- 구조
```text
상위 계층 → 신뢰 계층 → 비신뢰 채널 → 신뢰 계층 → 상위 계층
```

### 송신자(Sender) 동작

```text
1. Application Layer에서 데이터를 생성한다.
2. Application이 rdt_send(data)를 호출한다.
   → 이 함수는 "신뢰 계층에 데이터를 넘겨라"는 의미이다.
3. Reliable Data Transfer (Sending Side)는
   - 데이터를 패킷으로 만든다.
   - checksum을 추가한다.
   - 패킷을 생성한다.
4. 이후 udt_send(packet)을 호출한다.
   → 이 함수는 "이 패킷을 비신뢰적인 하위 채널로 보내라"는 의미이다.
5. 패킷은 Unreliable Channel로 전달된다.
```

### Unreliable Channel의 의미

- 이 채널은 신뢰할 수 없는 네트워크를 의미합니다.
  - 그래서 Transport Layer가 신뢰성을 보장해야 한다.
```text 
여기서는
- 패킷이 손상될 수 있다.
- 순서가 바뀔 수 있다.
- (버전에 따라) 손실될 수도 있다.
```

### 수신자(Receiver) 쪽 동작

```text
1. Unreliable Channel을 통해 패킷이 도착한다.
2. rdt_rcv(packet)이 호출된다.
   → 하위 채널에서 패킷을 받았다는 의미이다.
3. Reliable Data Transfer (Receiving Side)는
   - 패킷의 오류 여부를 검사한다.
   - checksum을 확인한다.
   - 정상 여부를 판단한다.
4. 정상이라면:
   - 데이터를 추출한다.
   - deliver_data(data)를 호출한다.
   → 상위 Application Layer로 데이터를 전달한다.
5. 오류가 있다면:
   - ACK/NAK 또는 재전송 제어를 수행한다.
   - 송신자에게 피드백을 보낸다.
```

---

## RDT 1.0 : 신뢰적인 채널에서 RDT

![02]()

- **Sender**
  - Wait for call from above : Application이 패킷을 보내주기를 대기하는 중입니다.
```text
  rdt_send(data) : rdt_send로 Application이 data를 보냅니다.
  packet : make_pkt로 data를 넣어서 패킷을 만듭니다.
  udt_send(packet) : 하위 계층으로 packet을 보냅니다.
```

- **Receiver**
  - Wait for call from below : 하위 채널로부터 패킷이 오기를 대기하는 중입니다.
```text
  rdt_rcv(packet) : rdt_send로 packet이 왔습니다.
  extract (packet, data) : 패킷에서 data를 꺼냅니다.
  deliver_data(data) : Application으로 data를 전달합니다.
```

---

## RDT 2.0 : 비트 오류가 있는 채널

### 수신측의 feedback 필요

- `acknowledgement (ACKs)` :  
receiver 가 sender 에게 pkt를 잘 받았다는 응답

- `negative acknowledgement (NAKs)` :  
receiver 가 sender 에게 pkt에 error또는 장애가 있다는 응답  
sender는 NAK인 경우 pkt 재전송

![03]()

- **Sender**
  - `Wait for call from above : Application이 패킷이 오기를 대기하는 중입니다.`
```text
  rdt_send(data) : rdt_send로 Application이 data를 보냅니다.
  snkpkt = make_pkt(data, checksum) : data와 함께 checksum을 추가하여, make_pkt 패킷을 만듭니다.
  udt_send(snkpkt) : udt_send로 snkpkt 패킷을 보냅니다.
```
  - `Wait for ACK OR NAK : 수신자의 응답이 ACK인지 NAK인지 기다립니다.`
```text
  rdt_rcv(rcvpkt) && isNAK(rcvpkt) : rdt_rcv으로 rcvpkt으로 왔고, 근데 NAK으로 Error 또는 장애가 있다고 응답 후  
  sender는 패킷을 재전송합니다.
  udt_send(sndpkt) : udt_send로 sndpkt 패킷을 보내는 것입니다.

  rdt_rcv(rcvpkt) && isACK(rcvpkt) : rdt_rcv로 패킷이 왔고, ACK로 잘 받았다고 응답을 합니다.
```

- **Receiver**
  - Wait for call from below : 하위 채널로부터 패킷이 오기를 대기하는 중입니다.
```text
  rdt_rcv(packet) && corrupt(rcvpkt) : rdt_send로 packet이 왔는데, corrupt로 패킷이 깨진것이다.
  송신자에게 다시 패킷을 전송해달라고 요청합니다.
  udt_send(NAK) : 오류가 있다고 응답합니다.

  rdt_rcv(packet) && notcorrupt(rcvpkt) : rdt_send로 packet이 왔는데, notcorrupt로 패킷이 깨지지않고 잘 왔다는 뜻입니다.
  extract (rcvpkt, data) : 패킷에서 data를 꺼냅니다.
  deliver_data(data) : Application으로 data를 전달합니다.
  udt_send(ACK) : 정상적으로 왔다고 응답합니다.
```

- 치명적인 결함 (STOP and WAIT)
```text
  Seneder는 패킷을 전송 후 Receiver로 부터 응답이 올때까지 대기합니다.
```

---

## RDT 2.1 : sender, receiver

### Sender

![03]()

- **Sender**
  - `Wait for call 0 from above : Application이 패킷이 오기를 대기하는 중입니다.`
```text
  rdt_send(data) : rdt_send로 Application이 data를 보냅니다.
  snkpkt = make_pkt(0, data, checksum) : 시퀀스 0번으로 data와 함께 checksum을 추가하여, make_pkt 패킷을 만듭니다.
  udt_send(snkpkt) : udt_send로 snkpkt 패킷을 보냅니다.
```
  - `Wait for ACK OR NAK 0 : 수신자의 응답이 ACK인지 NAK인지 기다립니다.`
```text
  rdt_rcv(rcvpkt) && corrupt(rcvpkt)||isNAK(rcvpkt) : rdt_rcv으로 rcvpkt으로 왔고,  
  corrupt 패킷이 깨지거나 NAK면 sender는 패킷을 재전송합니다.

  udt_send(sndpkt) : udt_send로 sndpkt 패킷을 보내는 것입니다.

  rdt_rcv(rcvpkt) && isACK(rcvpkt) : rdt_rcv로 패킷이 왔고, ACK로 잘 받았다고 응답을 합니다. 시퀀스 번호를 1번으로 변경합니다.
```

### Receiver

![04]()

- `Wait for call 0 from below : 현재 0번 패킷을 기다리는 상태`

#### 정상 패킷 도착 (seq0 + 손상 없음)

- `조건`
```text
   rdt_rcv(rcvpkt) && notcorrupt(rcvpkt) && has_seq0(rcvpkt) : rdt_rcv로 패킷이 오고,  
   패킷이 깨지지않고, 0번이 패킷이 왔습니다.
```

- `동작`
```text
   extract(rcvpkt, data) : 데이터를 추출합니다.
   deliver_data(data) : 상위계층에 데이터를 전달합니다.
   sndpkt = make_pkt(ACK, checksum) : checksum을 추가하여 ACK을 생성합니다.
   udt_send(sndpkt) : ACK 패킷을 전송합니다.
```

- 이후 상태는 `Wait for 1 from below` 정상적으로 0번을 받으면 이제 1번을 기다립니다.

#### 패킷이 손상된 경우

- `조건`
```text
   rdt_rcv(rcvpkt) && corrupt(rcvpkt) : 패킷이 왔는데, 패킷이 깨진 상태입니다.
```

- `동작`
```text
   sndpkt = make_pkt(NAK, checksum) : checksum을 추가하여 NAK을 생성합니다.
   udt_send(sndpkt) : 패킷을 재전송 요청을 합니다.
```

- 이후로 상태는 그대로 유지하며, 여전히 0번을 기다립니다.

#### 이미 받은 중복 패킷이 도착한 경우

- `동작`
```text
   rdt_rcv(rcvpkt) && notcorrupt && has_seq1 : 패킷이 왔고, 깨지지 않았지만 1번 패킷이 왔습니다.
   ---
   sndpkt = make_pkt(ACK, checksum) : 기존 ACK 패킷을 생성합니다.
   udt_send(sndpkt) : 기존 ACK 재정송 하며, 데이터는 다시 전달하지 않습니다.
```

---

## RDT 2.2 : NAK가 없는 rdt

- NAK를 제거합니다.
- ACK만으로 오류를 감지하고 재전송을 수행합니다.

![05]()

### Sender

- `Wait for call 0 from above : 0번 패킷이 오기를 기다리는 상태`
```text
   rdt_send(data) : 데이터를 받습니다.
   sndpkt = make_pkt(0, data, checksum) : seq=0을 붙여 패킷 생성합니다.
   udt_send(sndpkt) : 패킷을 전송합니다.
```


- `Wait for ACK 0 : 0번 패킷에 대한 응답을 기다리는 상태`

#### 정상 ACK 도착

- `동작`
```
   rdt_rcv(rcvpkt) && notcorrupt(rcvpkt) && isACK(rcvpkt, 0) :
   패킷이 왔고, 패킷이 깨지지 않으며, 0번 패킷으로 잘 전달되었음을 확인할수 있습니다.  
```

- 다음 상태 (Wait for call 1)로 됩니다.

#### 잘못된 ACK 또는 손상된 ACK 도착

- `동작`
```text
   rdt_rcv(rcvpkt) && (corrupt(rcvpkt) || isACK(rcvpkt,1)) :
   패킷이 왔지만 패킷이 깨지거나 시퀀스 1번인 패킷이 도착한 것입니다.
   udt_send(sndpkt) : 중복 패킷이거나 원하는 0번 패킷이 아니면 재전송을 요청합니다.
```

### Receiver 

- `Wait for 1 from below :  sequence 번호 1인 패킷을 기다리는 상태`

#### 정상적으로 패킷이 도착한 상황

- `동작`
```text
   rdt_rcv(rcvpkt) && (corrupt(rcvpkt) || has_seq1(rcvpkt)) :
   패킷이 손상되지 않았고, 기다리던 seq=1 패킷이 도착했습니다.
   ---
   extract(rcvpkt, data) :패킷에서 데이터 추출합니다.
   deliver_data(data) : 상위 계층으로 데이터 전달합니다.
   sndpkt = make_pkt(ACK1, chksum) : ACK1 생성, 이제 다음에 seq=1을 기다리겠다는 의미입니다.
   udt_send(sndpkt) : ACK1을 전송합니다.
```

#### 잘못된 상황

- `동작`
```text
   rdt_rcv(rcvpkt) && (corrupt(rcvpkt) || has_seq1(rcvpkt) :
   패킷이 왔고, 패킷이 깨지거나 1번 패킷이 도착한 것입니다.
   udt_send(sndpkt)
```

- NAK를 보내지 않고 이전 ACK를 다시 보내는 방식으로 송신자에게 재전송을 유도합니다.

---

## RDT 3.0 : 타이머의 등장

- bit error 가능
- ACK 손상 가능
- 패킷 손실 가능

### 전체 구조 개념

- sequence 번호는 0과 1을 번갈아 사용합니다.
- Stop-and-Wait 방식입니.
- 한 번에 하나의 패킷만 전송합니다.

- 차이점: ACK가 오지 않을시 타이머가 만료되어 자동 재전송합니다.

---

### RDT 3.0 전송 과정

- `Wait for call 0 from above : seq0 패킷을 보낼 준비 상태`

- `동작`
```text
   rdt_send(data)
   ---
   sndpkt = make_pkt(0, data, checksum) : 데이터에 sequence 번호 0과 오류 검사용 checksum을 붙여 패킷 생성합니다.
   udt_send(sndpkt) : 하위 비신뢰 채널로 실제 전송 수행
   start_timer() : ACK이 오지 않을 경우를 대비하여 타이머 시작합니다. 패킷 손실을 감지하기 위한 장치
```

- `Wait for ACK0 : 0번 패킷에 대한 확인 응답을 기다리는 상태`

#### 정상 ACK0 도착

- `동작`
```text
   rdt_rcv(rcvpkt) && notcorrupt(rcvpkt) && isACK(rcvpkt,0) :
   패킷이 왔으며, 깨지지 않고, seq = 0인 패킷이 정상적으로 전달되었습니다.
   ---
   stop_timer() : 정상 수신 확인되었으므로 재전송 필요 없습니다.
```

- 이후로 Wait for call 1 from above 다음 sequence 번호로 전환합니다.

#### 손상된 ACK 또는 잘못된 ACK1 도착

- `동작`
```text
   rdt_rcv(rcvpkt) && (corrupt(rcvpkt) || isACK(rcvpkt,1)) :
   패킷이 왔지만, 패킷이 깨졌거나 seq 번호가 다른 ACK 패킷이 온 것입니다.
```

- 이 상태는 아무것도 하지 않으며 무시하게 되며 Time Out이 발생합니다.

- `Time Out`
```text
   udt_send(sndpkt) : 이전에 보낸 동일 패킷 재전송합니다.
   start_timer() : 재전송했으므로 다시 타이머를 시작힙니다.
```

- ACK 일정 시간안에 정상적으로 도착했다고 안 햇으므로  
패킷 또는 손실 되었다고 생각을 하여 재전송 하게 됩니다.
