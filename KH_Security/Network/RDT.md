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

- **Sender**
  - `Wait for call 0 from below : 시퀀스가 0인 상태로 패킷이 오기를 대기합니다.`
```text

```
