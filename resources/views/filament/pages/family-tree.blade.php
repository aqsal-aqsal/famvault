<x-filament-panels::page>
    <div 
        x-data="familyTreeVisualizer(@js($this->getTreeData()))"
        class="w-full flex flex-col bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden"
    >
        <!-- Toolbar -->
        <div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50">
            <div class="flex items-center space-x-3">
                <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Interactive Canvas</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 font-medium" x-text="members.length + ' Members'"></span>
            </div>
            <div class="flex items-center space-x-2">
                <button 
                    @click="zoomIn()" 
                    class="p-2 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-xs font-semibold"
                    title="Zoom In"
                >
                    + Zoom In
                </button>
                <button 
                    @click="zoomOut()" 
                    class="p-2 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-xs font-semibold"
                    title="Zoom Out"
                >
                    - Zoom Out
                </button>
                <button 
                    @click="resetView()" 
                    class="p-2 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-xs font-semibold"
                >
                    Reset
                </button>
            </div>
        </div>

        <!-- Canvas Area -->
        <div 
            class="w-full h-[650px] relative overflow-hidden cursor-grab active:cursor-grabbing bg-zinc-100 dark:bg-zinc-950"
            @mousedown="startPan($event)"
            @mousemove="pan($event)"
            @mouseup="endPan()"
            @mouseleave="endPan()"
            @wheel.prevent="handleWheel($event)"
        >
            <div 
                class="absolute transition-transform duration-75 origin-top-left"
                :style="`transform: translate(${panX}px, ${panY}px) scale(${zoomLevel});`"
            >
                <!-- SVG Connector Lines -->
                <svg class="absolute top-0 left-0 w-[4000px] h-[3000px] pointer-events-none z-0">
                    <template x-for="link in links" :key="link.id">
                        <path 
                            :d="link.path" 
                            fill="none" 
                            stroke="currentColor" 
                            class="text-zinc-300 dark:text-zinc-700" 
                            stroke-width="2"
                            stroke-dasharray="4"
                        />
                    </template>
                </svg>

                <!-- Member Nodes -->
                <div class="relative z-10 w-[4000px] h-[3000px] p-20">
                    <template x-for="node in nodes" :key="node.id">
                        <div 
                            class="absolute transform -translate-x-1/2 -translate-y-1/2 transition-shadow duration-200"
                            :style="`left: ${node.x}px; top: ${node.y}px; width: 220px;`"
                        >
                            <div 
                                class="rounded-xl border p-4 shadow-sm bg-white dark:bg-zinc-900 hover:shadow-md transition-all cursor-pointer"
                                :class="node.gender === 'female' ? 'border-rose-300 dark:border-rose-900/60' : 'border-sky-300 dark:border-sky-900/60'"
                                @click="selectedMember = node"
                            >
                                <div class="flex items-center space-x-3">
                                    <div class="relative shrink-0">
                                        <img 
                                            :src="node.photo || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(node.name) + '&background=18181b&color=f59e0b')"
                                            class="w-12 h-12 rounded-full object-cover border-2"
                                            :class="node.gender === 'female' ? 'border-rose-400' : 'border-sky-400'"
                                            alt=""
                                        />
                                        <span 
                                            class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full text-[10px] flex items-center justify-center font-bold text-white"
                                            :class="node.gender === 'female' ? 'bg-rose-500' : 'bg-sky-500'"
                                            x-text="node.gender === 'female' ? '♀' : '♂'"
                                        ></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate" x-text="node.name"></h4>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate" x-text="node.nickname ? '(' + node.nickname + ')' : ''"></p>
                                        <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">
                                            <span x-text="node.birth || '?'"></span>
                                            <span x-show="node.death" x-text="' - ' + node.death"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Selected Member Drawer / Info -->
        <template x-if="selectedMember">
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/70 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-bold text-zinc-800 dark:text-zinc-200" x-text="selectedMember.name"></span>
                    <span class="text-xs text-zinc-500" x-text="'ID #' + selectedMember.id"></span>
                </div>
                <button @click="selectedMember = null" class="text-xs text-zinc-400 hover:text-zinc-600">Close</button>
            </div>
        </template>
    </div>

    <script>
        function familyTreeVisualizer(initialMembers) {
            return {
                members: initialMembers || [],
                nodes: [],
                links: [],
                zoomLevel: 1,
                panX: 100,
                panY: 50,
                isPanning: false,
                startX: 0,
                startY: 0,
                selectedMember: null,

                init() {
                    this.computeLayout();
                },

                computeLayout() {
                    if (!this.members.length) return;

                    // Group by generational level
                    const levels = {};
                    const visited = new Set();

                    const getLevel = (memberId, currentLevel = 0) => {
                        const m = this.members.find(x => x.id === memberId);
                        if (!m) return currentLevel;
                        let maxParentLevel = currentLevel;
                        if (m.father) {
                            maxParentLevel = Math.max(maxParentLevel, getLevel(m.father, currentLevel + 1));
                        }
                        if (m.mother) {
                            maxParentLevel = Math.max(maxParentLevel, getLevel(m.mother, currentLevel + 1));
                        }
                        return maxParentLevel;
                    };

                    const memberLevels = this.members.map(m => ({
                        ...m,
                        level: getLevel(m.id, 0)
                    }));

                    const maxLevel = Math.max(...memberLevels.map(m => m.level), 0);
                    // Invert so root ancestors are at top level 0
                    const nodePositions = {};
                    const levelBuckets = {};

                    memberLevels.forEach(m => {
                        const gen = maxLevel - m.level;
                        if (!levelBuckets[gen]) levelBuckets[gen] = [];
                        levelBuckets[gen].push(m);
                    });

                    const LEVEL_HEIGHT = 160;
                    const NODE_WIDTH = 260;

                    this.nodes = [];
                    Object.keys(levelBuckets).forEach(genStr => {
                        const gen = parseInt(genStr, 10);
                        const list = levelBuckets[gen];
                        list.forEach((m, idx) => {
                            const x = 300 + (idx * NODE_WIDTH);
                            const y = 100 + (gen * LEVEL_HEIGHT);
                            const node = { ...m, x, y };
                            this.nodes.push(node);
                            nodePositions[m.id] = node;
                        });
                    });

                    // Build connecting paths
                    this.links = [];
                    this.nodes.forEach(child => {
                        ['father', 'mother'].forEach(pKey => {
                            const pId = child[pKey];
                            if (pId && nodePositions[pId]) {
                                const parent = nodePositions[pId];
                                const x1 = parent.x;
                                const y1 = parent.y + 40;
                                const x2 = child.x;
                                const y2 = child.y - 40;
                                const midY = (y1 + y2) / 2;
                                const path = `M ${x1} ${y1} C ${x1} ${midY}, ${x2} ${midY}, ${x2} ${y2}`;
                                this.links.push({
                                    id: `${pId}->${child.id}`,
                                    path: path
                                });
                            }
                        });
                    });
                },

                zoomIn() {
                    this.zoomLevel = Math.min(2.0, this.zoomLevel + 0.15);
                },

                zoomOut() {
                    this.zoomLevel = Math.max(0.4, this.zoomLevel - 0.15);
                },

                resetView() {
                    this.zoomLevel = 1;
                    this.panX = 100;
                    this.panY = 50;
                },

                handleWheel(e) {
                    if (e.deltaY < 0) this.zoomIn();
                    else this.zoomOut();
                },

                startPan(e) {
                    this.isPanning = true;
                    this.startX = e.clientX - this.panX;
                    this.startY = e.clientY - this.panY;
                },

                pan(e) {
                    if (!this.isPanning) return;
                    this.panX = e.clientX - this.startX;
                    this.panY = e.clientY - this.startY;
                },

                endPan() {
                    this.isPanning = false;
                }
            };
        }
    </script>
</x-filament-panels::page>
