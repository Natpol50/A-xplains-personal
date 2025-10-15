# A-XPLAINS

> A minimalist, security-focused platform for hosting in-depth technical courses.

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![License Code](https://img.shields.io/badge/License%20(Code)-MIT-green)](LICENSE-CODE)
[![License Content](https://img.shields.io/badge/License%20(Content)-CC%20BY--SA--NC%204.0-orange)](LICENSE-CONTENT)
[![Status](https://img.shields.io/badge/Status-V00.00.02%20Stable-brightgreen)](https://github.com/yourusername/a-xplains)

## 🎯 Philosophy

**Understanding > Memorizing**

A-XPLAINS is aimed to create courses that explain:
1. **The problem** (why do we need this?)
2. **The intuition** (how should we think about it?)
3. **The logic** (what's the reasoning?)
4. **The implementation** (how do we build it?)

The platform itself is intentionally minimal: no frameworks, no databases, no build steps. Just drop HTML files and Apache does the rest. The quality of teaching comes from the course content, not the platform.

---

## ✨ Features

- 🔍 **Auto-discovery**: Courses are automatically indexed by scanning `/courses/` directory
- 🏷️ **Metadata extraction**: Title, description, tags, and difficulty parsed from HTML, no need to integrate them, just follwo the "template"
- 📂 **Category organization**: Courses grouped by folder structure so you don't have to bother too much
- 🔐 **Security-first**: 
  - PHP disabled in `/courses/` directory
  - Direct access to courses blocked (proxy via `view.php`)
  - Path traversal protection
  - Whitelist validation (extension, filename pattern, file size)
- 🎨 **Unified design system**: Shared CSS/JS loaded from `/assets/` [Should one day be modified]
- 📱 **Responsive**: Mobile-friendly interface [Mostly]

---

## 🚀 Quick Start

### Prerequisites

- Apache 2.4+ with `mod_rewrite`, `mod_ssl`, `mod_headers`, `mod_deflate`
- PHP 7.4+ with `DOM` extension
- SSL certificate (Let's Encrypt recommended)

### Installation

```bash
# Clone the repository
git clone https://github.com/natpol50/a-xplains.git
cd a-xplains

# Create required directories
mkdir -p cache courses

# Set permissions
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod 775 cache

# Create Apache VHost (see docs/apache-vhost.conf.example)
sudo nano /etc/apache2/sites-available/cours.yourdomain.com.conf

# Enable site and modules
sudo a2ensite cours.yourdomain.com.conf
sudo a2enmod rewrite ssl headers deflate expires
sudo systemctl reload apache2

# Install SSL certificate
sudo certbot --apache -d cours.yourdomain.com
```

### Adding a Course

```bash
# Create course file
nano courses/security/my-course.html
```

Minimal course structure:

```html
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>My Course - A Xplains</title>
    <link rel="stylesheet" href="/assets/a-xplains.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>My Course Title</h1>
            <p class="subtitle">Short description</p>
            <span class="badge badge-warning"><span>TAG</span></span>
        </header>
        <main>
            <section class="section active">
                <div class="content-box">
                    <h2>Content here</h2>
                </div>
            </section>
        </main>
    </div>
    <script src="/assets/a-xplains.min.js"></script>
</body>
</html>
```

**Refresh the cache:**
```bash
curl https://sub.yourdomain.com/refresh.php?token=your-secret-token
```

Your course then appears automatically on the index.

---

## 📁 Directory Structure

```
a-xplains/
├── index.php              # Auto-generated course index
├── view.php               # Secure course viewer (proxy)
├── config.php             # Security rules & metadata extraction
├── refresh.php            # Cache invalidation endpoint
├── .htaccess              # Apache security configuration
├── cache/
│   └── courses.json       # Metadata cache (auto-generated)
├── assets/
│   ├── a-xplains.min.css  # Unified design system
│   └── a-xplains.min.js   # Navigation, code copy, utilities
└── courses/
    ├── security/
    │   ├── shell-injection.html
    │   └── buffer-overflow.html
    ├── programming/
    │   └── memory-management.html
    └── math/
        └── calculus.html
```

---

## 🔒 Security Model

### How It Works

1. **Courses are static HTML** - No server-side code execution
2. **PHP disabled in `/courses/`** - `.htaccess` prevents PHP engine
3. **Direct access blocked** - Courses served only via `view.php` proxy
4. **Input validation**:
   - Extension whitelist (`.html` only)
   - Filename pattern check (`/^[a-zA-Z0-9_\-]+\.html$/`)
   - Path traversal protection (`realpath()` + base path check)
   - File size limit (5MB max)
5. **Metadata caching** - Reduces attack surface by limiting filesystem reads

### Threat Model

✅ **Protected against:**
- Remote code execution (RCE)
- Path traversal attacks
- PHP injection in course content
- Malicious file uploads (if you add upload feature)

⚠️ **Not protected against:**
- XSS in course content (trust your authors!)
- DoS attacks (use rate limiting at reverse proxy level)

---

## ⚡ Performance Optimizations

A-XPLAINS V00.00.02 includes several performance optimizations:

- **JSON metadata cache**: Courses are parsed once, cached for 1 hour
- **Gzip compression**: Reduces bandwidth by ~80%
- **Browser caching**: Assets cached for 1 year, HTML for 1 hour
- **Minified assets**: CSS/JS compressed for faster delivery
- **HTML minification**: Optional server-side minification in `view.php`

**Average response times:**
- First visit: ~200ms
- Cached visits: ~20ms
- Assets (cached): < 5ms

---

## 🎨 Design System

A-XPLAINS uses a custom design system inspired by various aesthetics, such that i cannot really list them:

- **Colors**: Dark gray background (`#262732`), green accents (`#658C79`)
- **Shapes**: Trapezoid buttons/badges with skew transforms
- **Typography**: Monospace for code, sans-serif for text [I'd prefer aptos, but eh, compatibility people]
- **Responsive**: Mobile-compatible with the good ol' 768px breakpoint

Most styling is centralized in `assets/a-xplains.min.css` for consistency across courses. A course can still use it's own css and JS if they ever need a specific system.

---

## 📚 Course Creation Guide

### Pedagogical Structure (Recommended)

Every course should follow a logical progression:

1. **Introduction Intuitive**: Real-world analogy, the problem it solves
2. **Theoretical Foundations**: Explain WHY before HOW
3. **Interactive Demonstrations**: Commented code, modifiable examples
4. **Structured Comparisons**: Tables, pros/cons, when to use what
5. **Practical Applications**: Real use cases, common pitfalls + solutions
6. **Knowledge Validation**: Quizzes, exercises with solutions
7. **Final Synthesis**: Key points (3-5 bullets), comprehension checklist

*Note: This is a recommendation, not a requirement. The platform doesn't enforce any structure.*

### Available CSS Classes

**Layout**
- `.container`: Main wrapper (max-width: 1280px)
- `.content-box`: Content box with dark background
- `.section`: Hidden by default, `.section.active` for visible

**Buttons**
- `.btn-primary`: Trapezoid button (always wrap text in `<span>`)

**Text**
- `h1`, `h2`, `h3`: Styled headers
- `.subtitle`: Gray subtitle text
- `.highlight`: Inline highlight with green background
- `strong`: Green emphasis

**Badges**
- `.badge.badge-danger`: Red badge (critical)
- `.badge.badge-warning`: Orange badge (warning)
- `.badge.badge-success`: Green badge (safe)

**Code**
- `.code-block`: Code container with copy button
- Use `<button class="copy-btn" onclick="copyCode(this)"><span>Copier</span></button>`

**Interactive**
- `.simulator`: Interactive area with green left border
- `.input-group`: Label + input grouping
- `.result`: Result box (red border by default)
- `.result.safe`: Result box with green border
- `.warning-box`: Alert box with automatic danger badge

**Tables**
- `.comparison-table`: Styled comparison table

### JavaScript Functions

From `assets/a-xplains.min.js`:
- **Navigation**: Automatic via `data-section` attributes
- `copyCode(button)`: Copy code block content
- `escapeHtml(text)`: Escape HTML characters

Add custom functions in a separate `<script>` block after importing the main JS.

---

## 🛣️ Roadmap

### V1 (Current 00.00.02) - Nada
- [x] Auto-discovery of courses (with very basic caching)
- [x] Metadata extraction from HTML (needs fixing)
- [x] Category-based organization (needs fixing on dynamic badges)
- [x] Search functionality (needs optimisation)
- [x] Secure file serving
- [x] allows for small exercices
- [ ] Unified design system
- [ ] Some Performance optimizations (caching, compression)

### V1/2 (Maybe one day) - Better Contribution Workflow
- [ ] Web-based course editor (WYSIWYG or markdown)
- [ ] Automated deployment pipeline
- [ ] Version control integration
- [ ] Multi-author support with roles
- [ ] Course templates library
- [ ] Staging environment for previews
- [ ] Analytics (page views, completion tracking)
- [ ] Export to PDF/EPUB
- [ ] Progress tracking for learners
---

## 🤝 Contributing

### Current State (V00.00.02)

V00.00.02 is intentionally simple and optimized for single-author use. The deployment workflow is manual and requires server access.

**If you want to share a course:**

1. **Create your course** following the HTML structure guidelines
2. **Test it locally** using the provided design system
3. **Share it:**
   - Open a GitHub Issue with your course idea/file
   - Email the HTML file: asha@asha-services.org
   - Fork the repo and open a PR (I'll manually review and deploy)

**Note:** There's no automated deployment yet. Accepted courses are manually deployed to production.

### Future (V2)

V2 will include proper contribution workflows:
- Automated deployment (merge → production)
- Staging environment for PR previews
- Contributor guidelines with templates
- Automated validation/linting
- Multi-author collaboration tools

---

## 📊 Comparison with Other Solutions

| Feature | A-XPLAINS | WordPress + LMS | Moodle | Hugo/Jekyll |
|---------|-----------|-----------------|--------|-------------|
| Setup time | 5 min | 30+ min | Hours | 15+ min |
| Database required | ❌ | ✅ | ✅ | ❌ |
| Auto-discovery | ✅ | ❌ | ❌ | ❌ |
| Security model | Read-only HTML | Plugin vulnerabilities | Complex | Build-time only |
| Hosting cost | $5/mo VPS | $10-20/mo | $20+/mo | Free (static) |
| Dynamic search | ✅ | ✅ | ✅ | ⚠️ Client-side only |
| Course quality | Depends on author | Depends on author | Depends on author | Depends on author |
| Course redaction difficulty | Medium | Easy | Medium | Depends |

---

## 📄 License

A-XPLAINS uses a **dual license** to separate platform code from educational content:

### Platform Code (MIT License)

All PHP, JavaScript and configuration files are licensed under **MIT License** - See [LICENSE-CODE](LICENSE-CODE)

**You are free to:**
- ✅ Use the platform commercially (host courses for paying clients)
- ✅ Modify and redistribute the code
- ✅ Create proprietary forks
- ✅ Use in closed-source projects

**Conditions:**
- Include the original MIT license and copyright notice
- No warranty is provided

### Course Content (Creative Commons BY-SA-NC 4.0)

All HTML course files in `/courses/` as well as the provided CSS are licensed under **CC BY-SA-NC 4.0** - See [LICENSE-CONTENT](LICENSE-CONTENT)

**You are free to:**
- ✅ Share course content for educational purposes
- ✅ Adapt and remix courses (with attribution)
- ✅ Use in non-commercial educational settings

**Conditions:**
- 📝 **Attribution (BY)**: Give appropriate credit to original authors
- 🔄 **ShareAlike (SA)**: Distribute modifications under the same license
- 🚫 **NonCommercial (NC)**: No commercial use without permission

**Examples:**

| Use Case | Allowed? |
|----------|----------|
| Fork platform, create your own courses | ✅ Yes (platform is MIT) |
| Use platform to host paid courses you created | ✅ Yes (your content, your rules) |
| Sell access to original A-XPLAINS courses | ❌ No (content is NC) |
| Use courses in university teaching | ✅ Yes (non-commercial education) |
| Translate/adapt courses and share freely | ✅ Yes (with attribution + SA) |
| Include courses in corporate training | ⚠️ Requires permission |
| Appropriate yourself the visual style | ❌ No, do your own if you host one (content is BY and SA) |

**Want commercial rights to course content?** Contact: courses@asha-services.org

### Why Dual License?

- **Platform freedom**: Companies can use/modify the code without restrictions
- **Content protection**: Prevents commercial exploitation of educational materials
- **Open source compatible**: MIT code can be integrated into other projects
- **Educational focus**: NC clause keeps courses accessible and free

---

## 🙏 Credits & Inspiration

**Technical influences:**
- [MDN Web Docs](https://developer.mozilla.org/) - Comprehensive technical documentation
- [Exploit Education](https://exploit.education/) - Hands-on security learning platforms
- [The Unix Philosophy](https://en.wikipedia.org/wiki/Unix_philosophy) - Do one thing well

**Educational philosophy:**
- [3Blue1Brown](https://www.3blue1brown.com/) - Visual intuition before formulas [recommended ++]
- [Feynman Technique](https://en.wikipedia.org/wiki/Feynman_Technique) - Teach to understand

---

## 💬 Contact & Support

- **Issues**: [GitHub Issues](https://github.com/yourusername/a-xplains/issues)
- **Discussions**: [GitHub Discussions](https://github.com/yourusername/a-xplains/discussions)
- **Email**: asha@asha-services.org
- **Website**: [asha-services.org](https://asha-services.org)

---

## 🌟 Showcase

Using A-XPLAINS? Add your site here via PR:

- [cours.asha-services.org](https://cours.asha-services.org) - Various personal leaflet by Asha Geyon
- [course.asha-services.org](https://cours.asha-services.org) - Security & programming courses by Asha Geyon [meant to be personal, but why not share it ?]
- *Your site here!*

---

**The platform is a tool. The teaching quality depends on you.**
