<?php
    include '../diop.php';  // Adjust the path as needed if your config.php is outside the public directory
    $aiApiKey = AI_TOKEN_COMTRADE;


echo "hello Vadim";

 
// www.bashurov.net/telegram/comtrade_cv.php
// $msg = handleAI($text);


function handleAI($text) {
    global $aiApiKey;



$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);


curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => 'gpt-4o',
    'max_tokens' => 100,
   'messages' => [
        ['role' => 'user', 'content' => 'What is the weather like today?'], // Replace with $text or user input
    ],
]));




curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer '.$aiApiKey
    ]);


$result = curl_exec($ch);
curl_close($ch);

$response = json_decode($result, true);

// Extract the assistant's answer
$msg = $response['choices'][0]['message']['content'];
return $msg;
}




function generateEmbedding($text) {
     global $aiApiKey;
     
   
    $ch = curl_init();
   
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/embeddings');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
  
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer '.$aiApiKey
    ]);


   
    $postData = json_encode([
        'input' => $text,
        'model' => 'text-embedding-ada-002',
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $response = curl_exec($ch);


   
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    $result = json_decode($response, true);
    return $result['data'][0]['embedding'] ?? null;
}


function iocument($text) {

    $result = $mysql->query("SELECT * FROM users WHERE userid='$userId' LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $mysql->query("UPDATE users SET tg_name = '$tguser', first = '$first', last = '$last'  WHERE userid='$userId'");
    } else {
        $mysql->query("INSERT INTO users (userid, tg_name, first, last) VALUES ('$userId', '$tguser', '$first', '$last' )");
    }
    $mysql->close();

}

function splitText($text) {
    // Split by sentences or paragraphs
    return explode("\n", $text); // Splits into paragraphs
}


function indexDocument($chunks) {
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    foreach ($chunks as $x) {

        $embedding = generateEmbedding($x);
        $emb = json_encode($embedding);

        $q = "INSERT INTO embed_cv (content,embedding) VALUES ('$x', '$emb')";
        $mysql->query($q);
 

 //       $stmt = $pdo->prepare("INSERT INTO knowledge_base (content, embedding) VALUES (:content, :embedding)");
 //       $stmt->execute([
 //           ':content' => $chunk,
 //           ':embedding' => json_encode($embedding)
 //       ]);
    }
    
    $mysql->close();
    echo "<br>";
    echo "sql closed";
    echo $chanks;
    
}

// Example usage: Pass your CV text
$cvText2 = array(
'Bashurov Vadim Phone is +79290499831 /WhatsUp/Viber/Telegram',
'Vadim Email is bashurov@mail.ru',
'Vadim Bashurov works in XENZU.com from 2021 in Project “Lupa” to collect user images, to design user album for hardcopy printing as book with diﬀerent covers, themes, formats etc.',
'Vadim Bashurov is Researcher, UNN Lobachevskogo, N.Novgorod from 2020.',
'He printed article Numerical modelling to solve the filter problems. Last publication in Dec 2021 in British Engineering Journal.',
'Vadim Bashurov was Lua/Mobile game Evangelist, Corona SDK, Toronto from 2018 to 2020.',
'He Lectures of 2D mobile development using Lua in cross platform.',
'Vadim Bashurov was Researcher in VNIIEF, Sarov, N.Novgorod from 1986 to 2020.',
'He is expert in Numerical modelling, 3D-graphics and ML', 
'EDUCATION of Vadim is Moscow State University, mechanical-math department, 1981-1986.',
'Vadim SKILLS are Mobile game development, Symbian, iOS/Android with Lua Corona SDK, iOS Swift.', 
'He published 100+ apps in Apple Appstore.Vadim was US Top 1 in 2011-2012 with app Six Towers.',
'Vadim Bashurov is a famous developer of Soviet PC game Pole Chudes. He created the game in 1991. This game has more then 10 000 000 downloads in USSR.',
'Vadim Bashurov is a famous iOS developer. He created the game Six Towers in 2010. This game has more then 3 000 000 downloads in US. He scored more then 1 000 000 dollars via ads and in-app payments.',
);

$cvText = array(
  // THURSDAY, Oct 2 — 09:40–10:40
  'THU 09:40 Cardita — OVERVIEW OF THE CHALLENGES AND POTENTIAL SOLUTIONS IN SECURING DIGITAL ECOSYSTEM IN THE US — Saeed Tabar; Gelareh Towhidi; Tay Bryant',
  'THU 09:40 Cardita — Confirming or Exploring with AI? The Impact of Intuition and Analysis in Decision-Making Styles — Fatih Çetin; Markus Launer; Joanna Paliszkiewicz',
  'THU 09:40 Cardita — A Comparative Analysis of Artificial Intelligence (AI) Research in the United States and South Korea — DaeRyong Kim',
  'THU 09:40 Coquina — Digital IT transformation in sustainable development reporting in Poland — Ewa Stawicka',
  'THU 09:40 Coquina — Artificial Intelligence Compliance: Perceptions within Organizations — Carol Springer Sargent; Alex Koohang; Christopher Tsavatewa; Kevin Floyd; Salome Svanadze',
  'THU 09:40 Coquina — AI Ethics: Frameworks, Principles, and Future Directions — Marc Miller; Alex Koohang; Kevin Floyd; Carol Springer Sargent; Doyeon Lee',
  'THU 09:40 Sundial — Rebranding second-generation open-source intelligence: “It’s not your father’s OSINT” — Fred Hoffman; Brian Fuller',
  'THU 09:40 Sundial — How Mercyhurst’s CIRAT does OSINT – and why — Fred Hoffman',
  'THU 09:40 Sundial — The UN Cybercrime Treaty and AI: Navigating the Intersection of Technology and Global Policy — Angelica Marotta; Stuart Madnick',
  'THU 09:40 Conch — An Update on the Application of Agile Principles to Pedagogical Activities and Strategies — John Stewart; G. Alan Davis',
  'THU 09:40 Conch — Better Humans, Better Machines: Eugenic Ideology in Transhumanism and AI Futures — Nada Hashmi; Sydney Lodge; Cassidy R. Sugimoto; Thema Monroe-White',
  'THU 09:40 Conch — More than a chatbot: Human-centered AI for student engagement and academic efficiency — Angela Munoz',

  // THURSDAY, Oct 2 — 10:50–11:50
  'THU 10:50 Cardita — The impact of AI tools on undergraduate students’ academic performance: A case study of Peru — Valeria Lozano-Gomez; Christian Fernando Libaque-Saenz',
  'THU 10:50 Cardita — Enhancing student motivation through a hands-on Raspberry Pi activity in an introductory IST course — Jennifer Breese; Laura Cruz; Lawrence Dupak; Victor Sandoval; Srikar Lanka; Christopher Fullard',
  'THU 10:50 Cardita — Evaluations and outcomes of experiences of two cohorts in an information technology Doctor of Science program: A comparative analysis — Kembley Lingelbach; Valerie Mercer; Zoroayka V. Sandoval',
  'THU 10:50 Coquina — Credit Card Fraud Detection with Machine Learning and Generative AI: A Data-Driven Approach — Weizheng Gao',
  'THU 10:50 Coquina — Enhancing Business Education with AI, Generative AI, and Prompt Engineering — Weizheng Gao',
  'THU 10:50 Coquina — Analyzing the High School Student’s Path to a Career in Cyber Security — Kevin Slonka; Sushma Mishra; Peter Draus; Natalya Bromall',
  'THU 10:50 Sundial — The growing adoption of artificial intelligence (AI) to drive innovation, resulting in enhanced competitiveness — Jeffrey Harmon; Edward Lazaros; Allen Truell; Christopher Davison; Hesham Allam',
  'THU 10:50 Sundial — Student success in intelligent tutoring systems: An assessment of key factors — Jenny V. Nehring; George D. Hickman',
  'THU 10:50 Sundial — Impacts of Artificial Intelligence Applications on Prescriptive Analytics: Content Analysis Based on Systematic Literature Review — Assion Lawson-Body; Laurence Lawson-Body; Abdou Illia; Kamel Rouibah',
  'THU 10:50 Conch — A Conceptual Framework for Analogical Learning in SQL: Reframing Small Teaching Strategies for Transfer — Bob Mills; Kelly Fadel; Reagan Siggard',
  'THU 10:50 Conch — Determining the Relationship Between Happiness and Environmental Sustainability at the International Level — John Stewart; G. Alan Davis; Brynne Stewart',
  'THU 10:50 Conch — An Analysis of the Usage of ChatGPT and other Generative AI software among IT developers — Alan Peslak; Wendy Cecucci; Kiku Jones',

  // THURSDAY, Oct 2 — 13:30–14:30
  'THU 13:30 Cardita — A process model and evaluation for developing course materials for computing curriculum using AI — Andrew Besmer; Jason Watson; David Scibelli',
  'THU 13:30 Cardita — From prototype to persona: AI agents for decision support and cognitive extension — Michael Bumpus',
  'THU 13:30 Cardita — From the weight room to the classroom: student-generated data as a catalyst for engagement — Kevin Mentzer; Justin Ruff; Brian Wendry; Jason Price; Brittany Gouws',
  'THU 13:30 Coquina — Strengthening Cybersecurity Education: The Urgent Need to Integrate Maritime Cybersecurity into Cyber Programs — Karen Paullet',
  'THU 13:30 Coquina — Augmenting radiology through artificial intelligence: a bibliometric review of the evolving role of radiologists — Cherie Noteboom; Vahini Atluri; Andy Behrens',
  'THU 13:30 Coquina — TTV: Towards advancing text-to-video generation with generative AI models and a comprehensive study of model fidelity, performance, and human perception — Tasnim Akter Onisha; Hayden Wimmer; Carl Rebman',
  'THU 13:30 Sundial — UI/UX Design for Mental Health: Surveying College Students to Shape the Future of Wearable Devices — James Tanasyah; Elaina Pan; Yuhei Morimoto; Nan Sun',
  'THU 13:30 Sundial — How Artificial Intelligence Can Support the Sustainable Development of Organizations? Findings from a Literature Review — Piotr Pietrzak',
  'THU 13:30 Sundial — Examining Synergy on Digital Wallet Transformation into Super App: The Case of Yape — Michela Grados-Llosa; Michelle Rodriguez-Serra',
  'THU 13:30 Conch — Cyber Risk, Privacy, and the Legal Complexities of Age Verification for Adult Content Platforms — Alana Murray; Huma Chhipa; Johnathan Yerby',
  'THU 13:30 Conch — FROM LEARNING TO EARNING: LONGITUDINAL INSIGHTS INTO HUNGARIAN GEN Z STUDENTS’ USE OF GENERATIVE AI IN ACADEMIC AND BUSINESS CONTEXTS (2023–2025) — Peter Nagy; Boglarka Nagy-Toth; Beata Bittner; Adrian Szilard Nagy',
  'THU 13:30 Conch — Addressing Implicit Bias in Teaching and Assessment: Ethical Challenges and Evidence-Based Interventions — Jessica Schwartz; April Adams; Charles Lively',

  // THURSDAY, Oct 2 — 15:00–16:00
  'THU 15:00 Cardita — Cybersecurity Awareness Among Post-Secondary Students — Natalya Bromall; Peter Draus; Sushma Mishra; Kevin Slonka; Judit Trunkos',
  'THU 15:00 Cardita — STRATEGIC AND COMMUNICATION DRIVERS OF DIGITAL PLATFORM ADOPTION IN BEEKEEPING: A UTAUT-BASED INVESTIGATION — Ewa Wanda Ziemba; Ewa Wanda Maruszewska; Anna Karmańska',
  'THU 15:00 Cardita — Mobile app for identifying recyclable items using a convolutional neural network — George Stefanek; Kody Smart; Mark Kadah; Eric Shelton; Nathan Custin',
  'THU 15:00 Coquina — Breaking the Chain of Knowledge Transfer: AI Shadows Implicit, Explicit and Tacit Exchange — David Scibelli; Brian Stevens',
  'THU 15:00 Coquina — Leveraging AI-Driven Predictive Cyber Analytics for Proactive Threat Hunting in Enterprise Security — Mary Kotch; Jennifer Breese',
  'THU 15:00 Coquina — Leveraging Large Language Models and In-Context Learning for Construct Identification in Computational Social Science: A Case Study on Wearable Devices — Omar El-Gayar; Abdullah Wahbeh; Mohammad Abdel-Rahman; Ahmed Elnoshokaty; Tareq Nasralah',
  'THU 15:00 Sundial — The Impact of Digitalization on Peruvian Public Services — Raul Diaz-Parra; Christian Fernando Libaque-Saenz',
  'THU 15:00 Sundial — Assessment of Learning in IS: A Framework for GenAI and Learner Progression — Joseph Woodside; Fred Augustine; William Sause',
  'THU 15:00 Sundial — From Code to Concern: A Demographic Analysis of AI Challenges and Ethics in IT Development — Alan Peslak; Lisa Kovalchick',
  'THU 15:00 Conch — Proactive Detection of Tax Fraud Using Explainable AI Techniques: A Hybrid Approach — Anas AlSobeh; Mustafa Abo El Rob; Kamel Rouibah; Amani Shatnawi',
  'THU 15:00 Conch — Sharing Economy: Towards a Hybrid Regulation Theory — Funda Sarican',
  'THU 15:00 Conch — Assessing IS Learning Outcomes Effectively in the Age of GenAI — Matthew North; Tyson Riskas',

  // FRIDAY, Oct 3 — 09:40–10:40
  'FRI 09:40 Cardita — Impact of AI on HRM — Adela Povalej; Dušan Lesjak; Frederik Kohun; Kongkiti Pushavat',
  'FRI 09:40 Cardita — Generative AI and Emotional Intelligence Support and Development in Higher Education — Cassie Longhart-Thomas; Shaila Rana',
  'FRI 09:40 Cardita — Embracing AI in Higher Education: Redefining Teaching and Learning in the Digital Era. — Tala Najem; Leila Halawi',
  'FRI 09:40 Coquina — Users’ perceptions of voice assistants’ effectiveness: trust, intelligence, accuracy, usefulness — Dania Bilal; Li-Min Huang',
  'FRI 09:40 Coquina — Quantifying Shortfalls in Students’ AI-Supported Programming Practices — Pratibha Menon',
  'FRI 09:40 Coquina — The Evolution of Mastery Learning… DARTS — Tapan Sarkar',
  'FRI 09:40 Sundial — Systematic review of lattice-based cryptography for blockchain in the post-quantum era — Salim Arfaoui; Omar El-Gayar',
  'FRI 09:40 Sundial — Website Characteristics impact on Web Traffic and Legitimacy Classification for Phishing Detection — Angel Ojeda; Melanie Pérez; Cristhian Pagán; José Cruz',
  'FRI 09:40 Sundial — Perceptions of AI-Generated and Co-Created Music… — Neil Rigole; Zoroayka V. Sandoval; Shannon Beasley; Kembley Lingelbach',
  'FRI 09:40 Conch — Using AI to Spread Misinformation in Social Media Posts — Vasilka Chergarova; Mel Tomeo; Enas Albataineh; Wilfred Mutale; John J. Scarpino; Heidi Morgan',
  'FRI 09:40 Conch — Factors influencing integration of Digital Forensics with Student Information Systems — Alvino Moses; Tiko Iyamu',
  'FRI 09:40 Conch — Psychological contract & affective trust on intentions in B2C e-commerce — Wei Sha',

  // FRIDAY, Oct 3 — 10:50–11:50
  'FRI 10:50 Cardita — Precision Check: A Critical Look at the Reliability of AI Detection Tools — Karen Paullet; Jamie Pinchot; Evan Kinney; Tyler Stewart',
  'FRI 10:50 Cardita — Trends in Security Research: A Text Mining Approach — Siri Chandana Marrapu; Ganesh Panja; Vishesha Bitla; Wen-Bin Yu',
  'FRI 10:50 Cardita — A PLS-SEM mediation analysis of adoption intention of Banking Chatbots — Chandni Bansal; Rajni Goel; Krishan Kumar Pandey; Anuj Sharma',
  'FRI 10:50 Coquina — ICT Domains for Enabling Smart City — Irja Shaanika; Tiko Iyamu; Nosayaba Evbuomwan',
  'FRI 10:50 Coquina — Effectiveness of AI-Driven Tools in Improving Student Learning Outcomes — Myungjae Kwak',
  'FRI 10:50 Coquina — Teaching Programming with AI vs Web Design Courses — AZAD ALI; Ramesh Soni',
  'FRI 10:50 Sundial — Reducing transactional distance with AI: role of chatbots in online student satisfaction — Joseph Rene Corbeil; Maria Elena Corbeil',
  'FRI 10:50 Sundial — INCREASING FOOD SUPPLY… SMART FARMING TECHNOLOGIES — Edward Chen',
  'FRI 10:50 Sundial — Military experience using AI to drive innovation and productivity in logistics — Tomasz Jałowiec; Joanna Paliszkiewicz',
  'FRI 10:50 Conch — Predicting March Madness Champion using Statistical Modeling — Jack Sweeney; Suhong Li',
  'FRI 10:50 Conch — Global Drivers of AI Development: Empirical Study Using Global AI Index — Angel Ojeda-Castro; Angel Ojeda-Millán; Ana Ojeda-Millán; Juan Valera; José Cruz',
  'FRI 10:50 Conch — Digital Technologies for Strategic Transformation in Higher Education — José Méndez; Angel Ojeda; Edgardo Rosaly; Juan Rivera',

  // FRIDAY, Oct 3 — 13:30–14:30
  'FRI 13:30 Cardita — AI literacy and engagement in higher education stakeholders — Christopher P. Daniels',
  'FRI 13:30 Cardita — AI-Driven Code Reviews: Developer Experiences — Sebastian Castaldi',
  'FRI 13:30 Cardita — Generational Perception of Ransomware-as-a-Service (RaaS) — Prayaanshu Pradhan; Kevin Ong; Utsav KC; Nan Sun',
  'FRI 13:30 Coquina — Psychological motivations and mental health in cybercriminal behavior — Peyton Lutchkus; Charley Tyrrell',
  'FRI 13:30 Coquina — AI in social engineering: literature review via routine activity theory — Chloe Dzuba',
  'FRI 13:30 Coquina — Hack Back: Illegal but Ethical? — Donna Schaeffer; Jeree Spicer; Patrick Olson',
  'FRI 13:30 Sundial — Suspicious Cheering with Bits — Emil Eminov; Stephen Flowerday; Andrew Morin',
  'FRI 13:30 Sundial — Leveraging AI in Managerial Decision-Making — Matelier Numbi; Gaston Elongha',
  'FRI 13:30 Sundial — AI in Education and Research: Opportunities, Challenges, Ethics — Joanna Paliszkiewicz',
  'FRI 13:30 Conch — Cross-cultural Privacy Literacy in E-commerce — Jing Hua; Ping Wang',
  'FRI 13:30 Conch — Facing your digital footprint on college campuses — Reese Martin; Sushma Mishra',
  'FRI 13:30 Conch — Expanded ECM with Tangible/Intangible Benefits of MOOCs (Peru) — Marcelo Emmanuel Briceño-Egúsquiza; Michelle Rodriguez-Serra',

  // FRIDAY, Oct 3 — 14:50–15:50
  'FRI 14:50 Cardita — Intersectional factors & values influencing Internet usage in the US — Alan Peslak; Pratibha Menon',
  'FRI 14:50 Cardita — Privacy-preserving IoT threat detection with federated learning & differential privacy — Salim Arfaoui; Omar El-Gayar',
  'FRI 14:50 Cardita — Convergence of AI, Cybersecurity, E-Commerce, Supply Chain — Angel Ojeda; José Vidal; Rafael Padilla; Mónica Ocasio',
  'FRI 14:50 Coquina — SMOTE variants for healthcare imbalanced datasets — Dara Tourt; Queen Booker; Carl Rebman',
  'FRI 14:50 Coquina — Improving cybersecurity via explainable AI: systematic review — Shadrack Oriaro; Sushma Mishra',
  'FRI 14:50 Coquina — Authorized Authorship of AI — David Scibelli; Michael Whitney',
  'FRI 14:50 Sundial — Local governments’ social media engagement → trust, satisfaction, WOM (Lima-Peru) — Gustavo Peña-Rosell; Christian F. Libaque-Saenz',
  'FRI 14:50 Sundial — Detecting Phishing Emails for Healthcare Practitioners: domain-specific ensemble — Gaston Elongha; Michelle Liu',
  'FRI 14:50 Sundial — Merchants’ Typology of Digital Wallet Users in Peruvian Retail — Christian F. Libaque-Saenz; Mario Chong; Ana Luna; Juan S. Garcia-Pajoy; Lucas Machuca',
  'FRI 14:50 Conch — Beyond the Lecture: Mobile Apps for Learning Communities — Lakisha Simmons; Isaac Addae',
  'FRI 14:50 Conch — Digitalization & Dynamic Learning Capability on Org Performance: Peruvian SMEs — María Angélica Córdova-Heredia',
  'FRI 14:50 Conch — Financial insider threats: a cybersecurity STRIDE analysis — Chelsea Idensohn; Stephen Flowerday',

  // SATURDAY, Oct 4 — 09:40–10:40
  'SAT 09:40 Cardita — Banking Without Borders: What Drives Neobank Use in Latin America — Mathias Alonso Minchan Wolstrohn; Michelle Rodriguez-Serra',
  'SAT 09:40 Cardita — An Empirical Investigation of AI Adoption and Perception among IT Professionals — Grace Chen; Suhong Li',
  'SAT 09:40 Cardita — Ethical safeguards and vulnerabilities in large language models — Jordan Stuckey; Hayden Wimmer; Carl Rebman',
  'SAT 09:40 Coquina — Toward Smarter Cybersecurity: Leveraging AI for Software Understanding — Alan Stines',
  'SAT 09:40 Coquina — A data science framework for AI-driven innovation within organizations — Daniel Wu',
  'SAT 09:40 Coquina — Factors impacting upper-level IT course grades using machine learning — J. F. Yao; Daniel Wu; John Huang; Troy Strader; Tsu-Ming Chiang',
  'SAT 09:40 Sundial — Enhancing University Education with AI: Telegram Bot with RAG & External APIs — Vadim Bashurov; Paul Safonov',
  'SAT 09:40 Sundial — Designing DARTS: A Unified AI System for Tutoring… — Tapan Sarkar',
  'SAT 09:40 Sundial — Business Applications of AI and Generative AI — Sam Nataraj',
  'SAT 09:40 Conch — Enhancing Textbook Slide Decks with ChatGPT — Steven Schilhabel',
  'SAT 09:40 Conch — Conceptual DSS Framework for Detective Analytics — Rebecca Bure; Nontobeko Mlambo; Tiko Iyamu',
  'SAT 09:40 Conch — Design principles for KMS supporting tacit & procedural knowledge — Benjamin X. Hou; Omar El-Gayar; M. Tafiqur Rahman; Nevine Nawar',

  // SATURDAY, Oct 4 — 10:50–11:50
  'SAT 10:50 Cardita — FROM FEAR TO COMPETENCY: Guiding University Staff Through the GenAI Revolution — Brian Gardner; Jennifer Breese',
  'SAT 10:50 Cardita — Revolutionary or evolutionary? Role of AI in cybersecurity — Paul Nugent',
  'SAT 10:50 Cardita — Systematizing Research Collaboration in Higher Education: A Design Science Case — Steven Schilhabel; Kimberly Iversen',
  'SAT 10:50 Coquina — Teach Programming with Generative AI & MMTP — Bryan Marshall; Jaclyn Queen; Alison Shepherd; Brad Fowler; Peter Cardon',
  'SAT 10:50 Coquina — AI’s Impact on Centralization vs Decentralization of Decision-making — Tzuyi Chan; Peter Cardon',
  'SAT 10:50 Coquina — Organizational AI appliance deployer (OAAD) for adoption standardization — Pius A. Onobhayedo; Peter Cardon',
  'SAT 10:50 Sundial — Generative AI in Higher Education: Use, Ethics, Impact — Rajdeep Sah; Cameron Hagemaster; Arpan Adhikari; Ari Lee; Nan Sun',
  'SAT 10:50 Sundial — Prompt Engineering with ChatGPT for higher education engagement — Meagan Hamman; Tiko Iyamu',
  'SAT 10:50 Sundial — Protecting the US Defense Industrial Base against APT attacks — Amy Kulikowski',
);




// echo $cvText;

 indexDocument($cvText);




?>


