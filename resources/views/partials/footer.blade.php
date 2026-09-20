<footer class="footer">
    <div class="container footer-grid">
        <div>
            <x-brand />
            <p>A better way to buy and sell on campus. Connecting verified students at Delta State University, Abraka.
            </p>
        </div>
        <div>
            <h3>Explore DelsuMart</h3>
            <nav aria-label="Footer marketplace">
                <a href="{{ route('listings.index') }}">Browse marketplace</a>
                <a href="{{ route('listings.create') }}">Start selling</a>
                <a href="{{ route('home') }}#how-it-works">How
                    it works</a>
            </nav>
        </div>
        <div>
            <h3>Trade with confidence</h3>
            <nav aria-label="Footer safety">
                <a href="{{ route('home') }}#security">Safety & security</a>
                <a href="{{ route('kyc.show') }}">Student verification</a>
                <a href="{{ route('home') }}#faq">Frequently
                    asked questions</a>
            </nav>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>© {{ date('Y') }} DelsuMart. A DELSU student marketplace
            project.</span>
        <span>Made for campus life. Built on trust.</span>
    </div>
</footer>
