<div class="fixed inset-0 bg-black flex flex-col font-sans overflow-hidden" 
    x-data="videoChat($wire, '{{ $userId }}')"
    @keydown.window.space.prevent="skip()"
>
    <!-- Background: Stranger's Video (Full Screen) -->
    <div class="fixed inset-0 z-0 bg-[#020202]">
        <video id="remoteVideo" class="w-full h-full object-cover transition-all duration-1000" autoplay playsinline poster="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&q=80&w=1200"></video>
        <!-- Cinematic Vignette Overlay -->
        <div class="absolute inset-0 shadow-[inset_0_0_200px_rgba(0,0,0,0.8)] pointer-events-none"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-black/80 pointer-events-none"></div>

        <!-- Matching Indicator Overlay -->
        <div x-show="!onCall" class="absolute inset-0 flex items-center justify-center z-10 bg-black/40 backdrop-blur-sm transition-all">
            <div class="flex flex-col items-center gap-6">
                <div class="relative">
                    <div class="w-24 h-24 rounded-full border-4 border-brand/20 border-t-brand animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-12 h-12 rounded-full bg-brand/20 animate-pulse"></div>
                    </div>
                </div>
                <div class="text-center">
                    <h2 class="text-2xl font-black text-white tracking-tighter mb-2 uppercase">Searching for Explorer...</h2>
                    <p class="text-zinc-300 font-bold text-xs uppercase tracking-[0.3em]">Connecting to Student Network</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Overlay: Interests & My ID -->
    <div class="relative z-40 p-8 flex justify-between items-start pointer-events-none">
        <div class="animate-in fade-in slide-in-from-top-4 duration-700">
            <div class="px-4 py-2 rounded-xl bg-black/60 backdrop-blur-xl border border-white/10 flex items-center gap-3">
                <span class="text-[9px] font-black tracking-widest uppercase text-zinc-400">My System ID</span>
                <span class="text-[11px] font-black text-white bg-brand/20 px-2 py-0.5 rounded border border-brand/30">{{ $userId }}</span>
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-2 max-w-[50%] animate-in fade-in slide-in-from-top-4 duration-700 delay-200">
            @foreach($activeInterests as $interest)
                <span class="interest-tag border-white/5 text-[8px] font-black uppercase tracking-[0.2em]">{{ $interest }}</span>
            @endforeach
        </div>
    </div>

    <!-- Middle: Skip Button (Centered Bottom) -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-50 flex flex-col items-center gap-4">
        <button @click="skip()" class="skip-button group">
            <svg class="text-white group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
        </button>
        <span class="text-[8px] font-black tracking-[0.4em] uppercase text-zinc-100 drop-shadow-lg">Next Spacebar</span>
    </div>

    <!-- Bottom Left: Your Camera (PIP) -->
    <div class="absolute bottom-10 left-10 z-40 w-56 aspect-video rounded-2xl overflow-hidden border border-white/30 shadow-2xl animate-in fade-in slide-in-from-left-4 duration-1000">
        <video id="localVideo" class="w-full h-full object-cover" autoplay muted playsinline style="transform: scaleX(-1);"></video>
        <!-- PIP Overlay Label -->
        <div class="absolute bottom-3 left-3 flex items-center gap-1.5 bg-black/80 backdrop-blur-md px-2.5 py-1.5 rounded-lg border border-white/20">
            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
            <span class="text-[8px] font-black uppercase tracking-widest text-white">You</span>
        </div>
    </div>

    <!-- Bottom Right: Chat Log -->
    <div class="absolute bottom-10 right-10 z-40 w-80 flex flex-col gap-4 pointer-events-none animate-in fade-in slide-in-from-right-4 duration-1000">
        <div x-ref="chatLog" class="max-h-56 overflow-y-auto no-scrollbar flex flex-col gap-2 pointer-events-auto mask-fade-top">
            <template x-for="msg in messages">
                <div :class="msg.sender === 'You' ? 'items-end' : 'items-start'" class="flex flex-col">
                    <div class="px-5 py-2.5 rounded-2xl text-[12px] font-bold shadow-2xl" 
                         :class="msg.sender === 'You' ? 'bg-brand text-white shadow-brand/20' : 'bg-black/80 backdrop-blur-3xl border border-white/20 text-white'">
                         <p x-text="msg.text"></p>
                    </div>
                </div>
            </template>
            <div x-show="isTyping" class="flex gap-1.5 px-4 py-3 rounded-xl bg-black/80 backdrop-blur-3xl w-fit border border-white/20 shadow-2xl">
                <div class="typing-dot bg-white"></div><div class="typing-dot bg-white" style="animation-delay: 0.2s"></div><div class="typing-dot bg-white" style="animation-delay: 0.4s"></div>
            </div>
        </div>

        <div class="relative pointer-events-auto group">
            <input 
                type="text" 
                x-model="newMessage"
                @keydown.enter="sendMessage()"
                placeholder="Enter message..." 
                class="w-full bg-black/60 backdrop-blur-3xl border border-white/30 rounded-2xl py-4.5 px-6 focus:outline-none focus:border-brand/80 transition-all text-[12px] pr-14 shadow-2xl placeholder-zinc-300 font-bold tracking-wide text-white"
            >
            <button @click="sendMessage()" class="absolute right-5 top-1/2 -translate-y-1/2 text-zinc-300 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5-5 5M6 12h12"></path></svg>
            </button>
        </div>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .mask-fade-top { mask-image: linear-gradient(to top, black 80%, transparent 100%); -webkit-mask-image: linear-gradient(to top, black 80%, transparent 100%); }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('videoChat', (wire, userId) => ({
                wire: wire,
                userId: userId,
                remoteUserId: '',
                onCall: false,
                isTyping: false,
                messages: [],
                newMessage: '',
                peerConnection: null,
                localStream: null,
                candidateBuffer: [],
                iceServers: { 
                    iceServers: [
                        { urls: "stun:stun.l.google.com:19302" },
                        {
                            urls: "turns:global.relay.metered.ca:443?transport=tcp",
                            username: "{{ $turnUsername }}",
                            credential: "{{ $turnCredential }}",
                        },
                        {
                            urls: "turn:global.relay.metered.ca:443",
                            username: "{{ $turnUsername }}",
                            credential: "{{ $turnCredential }}",
                        }
                    ] 
                },

                async init() {
                    console.log('[System] Initializing VideoChat for:', this.userId);
                    await this.startCamera();
                    
                    window.Echo.channel('voice-call-channel').listen('WebRTCSignaling', (e) => {
                        this.handleSignal(e);
                    });

                    window.addEventListener('beforeunload', () => {
                        if (this.remoteUserId) this.wire.sendSignal(this.remoteUserId, { type: 'hangup' });
                    });
                    
                    this.startMatchmaking();
                },

                startMatchmaking() {
                    const broadcastInterval = setInterval(() => {
                        if (this.onCall) {
                            clearInterval(broadcastInterval);
                        } else {
                            console.log('[Matchmaking] Broadcasting ready signal...');
                            this.wire.broadcastReady();
                        }
                    }, 3000);
                },

                resetChat() {
                    console.log('[System] Resetting for next explorer...');
                    if (this.peerConnection) {
                        this.peerConnection.close();
                        this.peerConnection = null;
                    }
                    this.remoteUserId = '';
                    this.onCall = false;
                    this.messages = [];
                    document.getElementById('remoteVideo').srcObject = null;
                    this.startMatchmaking();
                },

                async startCamera() {
                    try {
                        const constraints = { 
                            audio: true, 
                            video: { 
                                width: { ideal: 1280 },
                                height: { ideal: 720 },
                                frameRate: { ideal: 30 }
                            } 
                        };
                        this.localStream = await navigator.mediaDevices.getUserMedia(constraints);
                        document.getElementById('localVideo').srcObject = this.localStream;
                        console.log('[Camera] HD stream active');
                    } catch (e) { console.error('[Camera] Access denied:', e); }
                },

                async skip() {
                    console.log('[System] Skipping connection...');
                    if (this.remoteUserId) {
                        this.wire.sendSignal(this.remoteUserId, { type: 'hangup' });
                    }
                    window.location.reload();
                },

                async startCall(targetId) {
                    console.log('[WebRTC] Starting call to:', targetId);
                    this.remoteUserId = targetId;
                    this.peerConnection = this.createPeerConnection(targetId);
                    
                    this.setVideoParameters(this.peerConnection);

                    const offer = await this.peerConnection.createOffer();
                    await this.peerConnection.setLocalDescription(offer);
                    this.wire.sendSignal(targetId, { type: 'offer', sdp: offer.sdp });
                },

                setVideoParameters(pc) {
                    pc.getTransceivers().forEach(transceiver => {
                        if (transceiver.sender.track && transceiver.sender.track.kind === 'video') {
                            const params = transceiver.sender.getParameters();
                            if (!params.encodings) params.encodings = [{}];
                            params.encodings[0].maxBitrate = 4000000; 
                            params.encodings[0].degradationPreference = 'maintain-resolution';
                            transceiver.sender.setParameters(params);
                            console.log('[WebRTC] HD Quality parameters applied');
                        }
                    });
                },

                sendMessage() {
                    if(this.newMessage.trim()) {
                        this.messages.push({ sender: 'You', text: this.newMessage });
                        this.newMessage = '';
                        this.$nextTick(() => { this.$refs.chatLog.scrollTop = this.$refs.chatLog.scrollHeight; });
                    }
                },

                async handleSignal(e) {
                    if (e.senderId === this.userId) return;

                    const signal = e.data;
                    console.log('[Signaling] Received:', signal.type, 'from:', e.senderId);
                    
                    // Tie-breaker: Only the "lexicographically higher" ID initiates the call
                    if (e.receiverId === 'all' && signal.type === 'ready' && !this.onCall) {
                        if (this.userId > e.senderId) {
                            console.log('[Matchmaking] I am the initiator. Calling:', e.senderId);
                            this.startCall(e.senderId);
                        } else {
                            console.log('[Matchmaking] I am the receiver. Waiting for offer from:', e.senderId);
                        }
                        return;
                    }

                    if (e.receiverId === this.userId) {
                        if (signal.type === 'offer') {
                            if (this.onCall) return; // Already connected
                            
                            console.log('[WebRTC] Handling offer...');
                            this.remoteUserId = e.senderId;
                            this.peerConnection = this.createPeerConnection(this.remoteUserId);
                            this.setVideoParameters(this.peerConnection);

                            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(signal));
                            const answer = await this.peerConnection.createAnswer();
                            await this.peerConnection.setLocalDescription(answer);
                            this.wire.sendSignal(this.remoteUserId, { type: 'answer', sdp: answer.sdp });
                            this.processBufferedCandidates();
                            this.onCall = true;
                        } else if (signal.type === 'answer' && this.peerConnection) {
                            if (this.peerConnection.signalingState === 'have-local-offer') {
                                console.log('[WebRTC] Handling answer...');
                                await this.peerConnection.setRemoteDescription(new RTCSessionDescription(signal));
                                this.processBufferedCandidates();
                                this.onCall = true;
                            }
                        } else if (signal.type === 'candidate') {
                            if (this.peerConnection && this.peerConnection.remoteDescription) {
                                await this.peerConnection.addIceCandidate(new RTCIceCandidate(signal.candidate)).catch(() => {});
                            } else {
                                this.candidateBuffer.push(signal.candidate);
                            }
                        } else if (signal.type === 'hangup') {
                            console.log('[System] Remote hangup received');
                            this.resetChat();
                        }
                    }
                },

                async processBufferedCandidates() {
                    while (this.candidateBuffer.length > 0) {
                        const candidate = this.candidateBuffer.shift();
                        if (this.peerConnection && this.peerConnection.remoteDescription) {
                            await this.peerConnection.addIceCandidate(new RTCIceCandidate(candidate)).catch(() => {});
                        }
                    }
                },

                createPeerConnection(targetId) {
                    const pc = new RTCPeerConnection({
                        ...this.iceServers,
                        iceTransportPolicy: 'all'
                    });
                    
                    this.localStream.getTracks().forEach(track => pc.addTrack(track, this.localStream));
                    
                    pc.onicecandidate = (event) => {
                        if (event.candidate) {
                            this.wire.sendSignal(targetId, { type: 'candidate', candidate: event.candidate });
                        }
                    };

                    pc.oniceconnectionstatechange = () => {
                        console.log('[WebRTC] ICE State:', pc.iceConnectionState);
                        if (pc.iceConnectionState === 'connected' || pc.iceConnectionState === 'completed') {
                            this.onCall = true;
                        }
                        if (pc.iceConnectionState === 'disconnected' || pc.iceConnectionState === 'failed' || pc.iceConnectionState === 'closed') {
                            this.resetChat();
                        }
                    };

                    pc.ontrack = (event) => {
                        const remoteVideo = document.getElementById('remoteVideo');
                        if (remoteVideo.srcObject !== event.streams[0]) {
                            remoteVideo.srcObject = event.streams[0];
                        }
                    };
                    return pc;
                }
            }));
        });
    </script>
</div>
