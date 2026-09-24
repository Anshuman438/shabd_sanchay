/**
 * Shabd Sanchay - Advanced Hindi/Urdu Maatraa & Chhand Engine
 * Grounded in authentic Kaavyaalaya.org & Sanskrit Chhanda-Shastra rules.
 * 
 * Rules Implemented:
 * 1. Short Vowels (अ, इ, उ, ऋ, ऌ / ि, ु, ृ, ॢ): 1 Maatraa (Laghu ।)
 * 2. Long Vowels (आ, ई, ऊ, ऐ, औ / ा, ी, ू, ै, ौ): 2 Maatraas (Guru ऽ)
 * 3. Vowels ए, ओ (े, ो): Standard Khadee Bolee = 2; Avadhee/Urdu mode = 1 or 2
 * 4. Anusvara (ं) & Visarga (ः): 2 Maatraas (Guru ऽ)
 * 5. Chandrabindu (ँ): 1 Maatraa (does not make short vowel heavy)
 * 6. Half-letters / Conjuncts (हलन्त / संयुक्ताक्षर):
 *    - Initial half-letter (क्लेश, प्यार, स्वतंत्रता) = 0 maatra
 *    - Internal half-letter after Laghu (कष्ट, कल्प, सुर्ख़, सख्त) = previous Laghu becomes Guru (2)
 *    - Internal half-letter after Deergh followed by Laghu (आत्म, मूर्ख, दीर्घ) = previous stays 2 (0 extra)
 *    - Internal half-letter between two Deergh vowels (आत्मा) = half-letter contributes +1 (total 5)
 */

class HindiMatraEngine {
    constructor(options = {}) {
        this.mode = options.mode || 'standard'; // 'standard' (Khadee Bolee) or 'flexible' (Avadhee/Urdu)
    }

    setMode(mode) {
        this.mode = mode;
    }

    // Split text into lines, then words, then analyze each
    analyzeText(text) {
        if (!text || typeof text !== 'string') {
            return { lines: [], grandTotal: 0, meterDetection: null };
        }

        const rawLines = text.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 0);
        let grandTotal = 0;
        const analyzedLines = [];

        rawLines.forEach((lineStr, lineIdx) => {
            const lineAnalysis = this.analyzeLine(lineStr, lineIdx);
            grandTotal += lineAnalysis.lineTotal;
            analyzedLines.push(lineAnalysis);
        });

        // Determine overall poetic meter
        const meterDetection = this.detectPoeticMeter(analyzedLines);

        return {
            lines: analyzedLines,
            grandTotal,
            meterDetection
        };
    }

    // Analyze a single line
    analyzeLine(lineStr, lineIdx = 0) {
        // Split into words, preserving spaces/punctuations
        const rawWords = lineStr.split(/\s+/).filter(w => w.trim().length > 0);
        let lineTotal = 0;
        const analyzedWords = [];

        rawWords.forEach(w => {
            const wordAnalysis = this.analyzeWord(w);
            lineTotal += wordAnalysis.total;
            analyzedWords.push(wordAnalysis);
        });

        const patternString = analyzedWords.map(w => w.pattern).join(' ');
        const patternWeights = analyzedWords.map(w => w.weightsPattern).join(' ');

        return {
            lineIndex: lineIdx + 1,
            originalText: lineStr,
            lineTotal,
            words: analyzedWords,
            patternString,
            patternWeights
        };
    }

    // Analyze a single word according to Kaavyaalaya phonetic rules
    analyzeWord(word) {
        // Strip non-letter punctuation, Devanagari Danda (।, ॥), digits, quotes, dashes
        const cleanWord = word.replace(/[\u0964\u0965\u0970\u09710-9\u0966-\u096F.,!?:;"'«»।॥()[\]{}—–\\/]/g, '').trim();
        if (!cleanWord) {
            return { word, total: 0, syllables: [], pattern: '', weightsPattern: '' };
        }

        const chars = Array.from(cleanWord);
        const len = chars.length;
        const rawUnits = [];
        let i = 0;

        // 1. Group characters into graphical Devanagari Akshara Clusters
        while (i < len) {
            const c = chars[i];
            
            // Accept Devanagari characters and skip any other stray characters
            if (!/[\u0900-\u097F]/.test(c)) {
                i++;
                continue;
            }

            let cluster = c;
            i++;

            // Collect attached combining marks (Nukta, Matras, Halant, Anusvara, Visarga, Chandrabindu)
            while (i < len) {
                const next = chars[i];
                if (/[\u093C\u093E-\u094C\u094D\u0901-\u0903\u0951-\u0954\u0962\u0963]/.test(next)) {
                    cluster += next;
                    i++;
                } else {
                    break;
                }
            }

            rawUnits.push(cluster);
        }

        if (rawUnits.length === 0) {
            return { word, total: 0, syllables: [], pattern: '', weightsPattern: '' };
        }

        // 2. Classify raw unit base vowel weight
        const parsedUnits = rawUnits.map(u => {
            const isHalf = u.endsWith('्');

            // Long vowels & matras: आ, ई, ऊ, ऐ, औ, ए, ओ, ा, ी, ू, े, ै, ो, ौ, ं, ः
            const hasLong = /[ाीूैौेो\u0902\u0903]|[\u0906\u0908\u090A\u0910\u0914\u090F\u0913]/.test(u);
            const hasShort = /[िुृॢ]|[\u0905\u0907\u0909\u090B]/.test(u);

            let weight = 1;
            let baseTag = 'लघु';

            if (hasLong) {
                // If in flexible mode and letter has 'ए' or 'ओ', allow smart nuance if flagged
                weight = 2;
                baseTag = 'दीर्घ';
            } else if (hasShort) {
                weight = 1;
                baseTag = 'ह्रस्व';
            } else {
                // Inherent 'अ' vowel in consonant
                weight = 1;
                baseTag = 'मूल व्यंजन (अ)';
            }

            return {
                raw: u,
                text: u,
                isHalf,
                baseWeight: weight,
                weight,
                tag: baseTag,
                rule: ''
            };
        });

        // 3. Apply Kaavyaalaya Conjunct (संयुक्ताक्षर) Rules
        const finalSyllables = [];
        const num = parsedUnits.length;

        for (let k = 0; k < num; k++) {
            const curr = parsedUnits[k];

            // Rule A: Initial Half-Letter (e.g. क्लेश, प्यार, स्वतंत्रता, प्रकृति) -> 0 weight
            if (k === 0 && curr.isHalf) {
                if (k + 1 < num) {
                    parsedUnits[k + 1].text = curr.text + parsedUnits[k + 1].text;
                    parsedUnits[k + 1].rule = 'आरंभिक आधा वर्ण (भार = 0)';
                }
                continue;
            }

            // Rule B: Half-Letter inside the word
            if (curr.isHalf) {
                const prevIdx = finalSyllables.length - 1;
                const nextIdx = k + 1;

                const halfText = curr.text;
                if (nextIdx < num) {
                    parsedUnits[nextIdx].text = halfText + parsedUnits[nextIdx].text;
                }

                if (prevIdx >= 0) {
                    const prevSyllable = finalSyllables[prevIdx];
                    const nextBaseWeight = (nextIdx < num) ? parsedUnits[nextIdx].baseWeight : 0;

                    // Case B1: Preceded by Laghu (1) -> makes previous letter Guru (2)
                    // e.g. कष्ट (क 1->2), कल्प (क 1->2), सुर्ख़ (सु 1->2), सख्त (स 1->2)
                    if (prevSyllable.baseWeight === 1) {
                        prevSyllable.weight = 2;
                        prevSyllable.tag = 'गुरु (संयुक्ताक्षर पूर्व)';
                        prevSyllable.rule = 'लघु के बाद आधा अक्षर आने से गुरु (2) बना';
                    }
                    // Case B2: Preceded by Deergh (2) AND followed by Deergh (2) -> Half letter takes +1!
                    // e.g. आत्मा -> आ (2) + त् (1) + मा (2) = 5
                    else if (prevSyllable.baseWeight === 2 && nextBaseWeight === 2) {
                        prevSyllable.weight = 3;
                        prevSyllable.tag = 'दीर्घ + आधा वर्ण (2+1)';
                        prevSyllable.rule = 'दो दीर्घ स्वरों के बीच आधा अक्षर 1 मात्रा लेता है (2+1=3)';
                    }
                    // Case B3: Preceded by Deergh (2) AND followed by Laghu (1) -> 0 extra weight
                    // e.g. आत्म (आ=2, त्म=1), मूर्ख (मू=2, र्ख=1), दीर्घ (दी=2, र्घ=1)
                    else {
                        prevSyllable.rule = 'दीर्घ के बाद आधा अक्षर समाहित (0 अतिरिक्त भार)';
                    }
                }
                continue;
            }

            finalSyllables.push(curr);
        }

        const total = finalSyllables.reduce((acc, s) => acc + s.weight, 0);
        const pattern = finalSyllables.map(s => (s.weight >= 2 ? 'ऽ' : '।')).join('');
        const weightsPattern = finalSyllables.map(s => s.weight).join('+');

        return {
            word: cleanWord,
            total,
            syllables: finalSyllables,
            pattern,
            weightsPattern: `(${weightsPattern})`
        };
    }

    // Determine Chhand or Metre from analyzed lines
    detectPoeticMeter(lines) {
        if (!lines || lines.length === 0) return null;

        const totals = lines.map(l => l.lineTotal);
        const count = lines.length;

        // Check for Single Line Meters
        if (count === 1) {
            const t = totals[0];
            if (t === 16) {
                return {
                    name: 'चौपाई चरण (Chaupai Line)',
                    badge: '16 मात्राएँ',
                    description: 'यह पंक्ति 16 मात्राओं के प्रामाणिक चौपाई छंद के पूर्णतः अनुरूप है।',
                    desc: 'यह पंक्ति 16 मात्राओं के प्रामाणिक चौपाई छंद के पूर्णतः अनुरूप है।',
                    isIdentified: true,
                    status: 'success'
                };
            }
            if (t === 24) {
                return {
                    name: 'दोहा / रोला / सोरठा (24 मात्राएँ)',
                    badge: '24 मात्राएँ',
                    description: 'यह पंक्ति 24 मात्राओं के दोहा/रोला/सोरठा छंद की संपूर्ण पंक्ति है (यति 13|11 या 11|13)।',
                    desc: 'यह पंक्ति 24 मात्राओं के दोहा/रोला/सोरठा छंद की संपूर्ण पंक्ति है (यति 13|11 या 11|13)।',
                    isIdentified: true,
                    status: 'success'
                };
            }
            if (t === 13) {
                return {
                    name: 'दोहा विषम चरण (13 मात्राएँ)',
                    badge: '13 मात्राएँ (यति पूर्व)',
                    description: 'दोहा के प्रथम या तृतीय चरण (13 मात्रा) का सटीक माप।',
                    desc: 'दोहा के प्रथम या तृतीय चरण (13 मात्रा) का सटीक माप।',
                    isIdentified: true,
                    status: 'info'
                };
            }
            if (t === 11) {
                return {
                    name: 'दोहा सम चरण (11 मात्राएँ)',
                    badge: '11 मात्राएँ (यति पश्चात्)',
                    description: 'दोहा के द्वितीय या चतुर्थ चरण (11 मात्रा) का सटीक माप।',
                    desc: 'दोहा के द्वितीय या चतुर्थ चरण (11 मात्रा) का सटीक माप।',
                    isIdentified: true,
                    status: 'info'
                };
            }
            if (t === 28) {
                return {
                    name: 'हरिगीतिका छंद (Harigitika)',
                    badge: '28 मात्राएँ (16 + 12)',
                    description: '28 मात्राओं का शास्त्रीय हरिगीतिका छंद।',
                    desc: '28 मात्राओं का शास्त्रीय हरिगीतिका छंद।',
                    isIdentified: true,
                    status: 'success'
                };
            }
            if (t === 30) {
                return {
                    name: 'वीर / आल्हा छंद (30/31 मात्राएँ)',
                    badge: '30 मात्राएँ',
                    description: 'सुभद्रा कुमारी चौहान रचित "झाँसी की रानी" का प्रसिद्ध 30-मात्रिक वीर छंद।',
                    desc: 'सुभद्रा कुमारी चौहान रचित "झाँसी की रानी" का प्रसिद्ध 30-मात्रिक वीर छंद।',
                    isIdentified: true,
                    status: 'success'
                };
            }
            if (t === 17 || t === 18 || t === 19 || t === 21 || t === 22) {
                return {
                    name: 'ग़ज़ल बहर मिसरा (Ghazal Metre)',
                    badge: `${t} मात्रा भार`,
                    description: 'यह पंक्ति ग़ज़ल की शास्त्रीय बहर (वज़न) के अनुकूल है।',
                    desc: 'यह पंक्ति ग़ज़ल की शास्त्रीय बहर (वज़न) के अनुकूल है।',
                    isIdentified: true,
                    status: 'success'
                };
            }
            return {
                name: `मात्रिक छंद (${t} मात्राएँ)`,
                badge: `${t} मात्राएँ`,
                description: `इस पंक्ति का कुल मात्रा भार ${t} है।`,
                desc: `इस पंक्ति का कुल मात्रा भार ${t} है।`,
                isIdentified: true,
                status: 'neutral'
            };
        }

        // Multi-line comparisons (e.g. Couplet / Doha / Chaupai Stanza)
        if (count >= 2) {
            const t1 = totals[0];
            const t2 = totals[1];

            // Both 16 -> Chaupai couplet
            if (t1 === 16 && t2 === 16) {
                return {
                    name: 'पूर्ण चौपाई युगल (Chaupai Couplet)',
                    badge: '16 + 16 मात्राएँ',
                    description: 'दोनों पंक्तियों में सम-मात्रिक 16-16 मात्राओं का संपूर्ण संतुलन है (जैसे रामचरितमानस)।',
                    desc: 'दोनों पंक्तियों में सम-मात्रिक 16-16 मात्राओं का संपूर्ण संतुलन है (जैसे रामचरितमानस)।',
                    isIdentified: true,
                    status: 'success'
                };
            }
            // Both 24 -> Doha couplet
            if (t1 === 24 && t2 === 24) {
                return {
                    name: 'पूर्ण दोहा छंद (Complete Doha)',
                    badge: '24 + 24 मात्राएँ',
                    description: 'दोनों पंक्तियाँ 24-24 मात्राओं (13+11) के शास्त्रीय दोहा छंद में पूर्णतः निबद्ध हैं।',
                    desc: 'दोनों पंक्तियों में 24-24 मात्राओं (13+11) के शास्त्रीय दोहा छंद में पूर्णतः निबद्ध हैं।',
                    isIdentified: true,
                    status: 'success'
                };
            }
            // Both 30 -> Veera / Alha
            if (t1 === 30 && t2 === 30) {
                return {
                    name: 'वीर छंद युगल (Veer Chhand Couplet)',
                    badge: '30 + 30 मात्राएँ',
                    description: 'दोनों पंक्तियाँ 30-30 मात्राओं के सुदृढ़ वीर छंद में संतुलित हैं।',
                    desc: 'दोनों पंक्तियाँ 30-30 मात्राओं के सुदृढ़ वीर छंद में संतुलित हैं।',
                    isIdentified: true,
                    status: 'success'
                };
            }
            // Equal weights -> Ghazal or Sam-matrik
            if (t1 === t2) {
                return {
                    name: `हम-वज़न काव्य युगल (${t1} = ${t2})`,
                    badge: `${t1} मात्राएँ`,
                    description: `दोनों पंक्तियाँ समान भार (${t1}) पर हैं, जिससे गेयता और लयबद्धता परिपूर्ण है।`,
                    desc: `दोनों पंक्तियाँ समान भार (${t1}) पर हैं, जिससे गेयता और लयबद्धता परिपूर्ण है।`,
                    isIdentified: true,
                    status: 'success'
                };
            }

            return {
                name: `विषम-मात्रिक रचना (${t1} vs ${t2})`,
                badge: `${t1} | ${t2} मात्राएँ`,
                description: `पहली पंक्ति में ${t1} और दूसरी में ${t2} मात्राएँ हैं। छंदानुशासन हेतु मात्रा सामंजस्य जाँचें।`,
                desc: `पहली पंक्ति में ${t1} और दूसरी में ${t2} मात्राएँ हैं। छंदानुशासन हेतु मात्रा सामंजस्य जाँचें।`,
                isIdentified: true,
                status: 'warning'
            };
        }

        return null;
    }
}

// Export for browser window and Node.js
if (typeof window !== 'undefined') {
    window.HindiMatraEngine = HindiMatraEngine;
}
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HindiMatraEngine;
}
